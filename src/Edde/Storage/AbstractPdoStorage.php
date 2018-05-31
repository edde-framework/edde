<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Collection\Entity;
	use Edde\Collection\EntityNotFoundException;
	use Edde\Collection\IEntity;
	use Edde\Config\ConfigException;
	use Edde\Query\Command;
	use Edde\Query\IChain;
	use Edde\Query\ICommand;
	use Edde\Query\IQuery;
	use Edde\Query\IWhere;
	use Edde\Service\Schema\SchemaManager;
	use Edde\Service\Security\RandomService;
	use Generator;
	use PDO;
	use PDOException;
	use Throwable;
	use function array_pop;
	use function implode;
	use function in_array;
	use function sha1;
	use function sprintf;
	use function strtoupper;
	use function vsprintf;

	abstract class AbstractPdoStorage extends AbstractStorage {
		use SchemaManager;
		use RandomService;
		/** @var array */
		protected $options;
		/** @var PDO */
		protected $pdo;

		/** @inheritdoc */
		public function __construct(string $config, array $options = []) {
			parent::__construct($config);
			$this->options = $options;
		}

		/** @inheritdoc */
		public function fetch($query, array $params = []) {
			try {
				$statement = $this->pdo->prepare($query);
				$statement->setFetchMode(PDO::FETCH_ASSOC);
				$statement->execute($params);
				return $statement;
			} catch (PDOException $exception) {
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function exec($query, array $params = []) {
			try {
				if (empty($params) === false) {
					throw new StorageException(sprintf('%s does not support params.', __METHOD__));
				}
				return $this->pdo->exec($query);
			} catch (PDOException $exception) {
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function query(IQuery $query): Generator {
			$command = $this->native($query);
			$schemas = $this->getSchemas($query);
			$selects = $query->getSelects();
			foreach ($this->fetch($command->getQuery(), $command->getParams()) as $row) {
				yield $this->row($row, $schemas, $selects);
			}
		}

		/** @inheritdoc */
		public function native(IQuery $query): ICommand {
			$params = $query->getParams();
			$selects = $query->getSelects();
			$attaches = $query->getAttaches();
			$count = $query->isCount();
			$schemas = $this->getSchemas($query);
			$select = [];
			$from = [];
			foreach ($selects as $alias => $schema) {
				foreach ($schemas[$schema]->getAttributes() as $name => $attribute) {
					$select[] = vsprintf($count ? 'COUNT(%s.%s) AS %s' : '%s.%s AS %s', [
						$this->delimit($alias),
						$this->delimit($name),
						$this->delimit($count ? $alias : $alias . '.' . $name),
					]);
				}
				if ($query->isAttached($alias)) {
					continue;
				}
				$from[] = vsprintf('%s %s', [
					$this->delimit($schemas[$schema]->getRealName()),
					$this->delimit($alias),
				]);
			}
			foreach ($attaches as $attach) {
				$sourceSchema = $schemas[$selects[$attach->attach]];
				$relationSchema = $schemas[$selects[$attach->relation]];
				$targetSchema = $schemas[$selects[$attach->to]];
				$this->checkRelation($relationSchema, $sourceSchema, $targetSchema);
				$from[] = vsprintf("%s %s\n\t\tINNER JOIN %s %s ON %2\$s.%s = %4\$s.%s\n\t\tINNER JOIN %s %s ON %2\$s.%s = %8\$s.%s", [
					$this->delimit($relationSchema->getRealName()),
					$this->delimit($attach->relation),
					$this->delimit($sourceSchema->getRealName()),
					$this->delimit($attach->attach),
					$this->delimit($relationSchema->getSource()->getName()),
					$this->delimit($sourceSchema->getPrimary()->getName()),
					$this->delimit($targetSchema->getRealName()),
					$this->delimit($attach->to),
					$this->delimit($relationSchema->getTarget()->getName()),
					$this->delimit($targetSchema->getPrimary()->getName()),
				]);
			}
			$sql = vsprintf("SELECT\n\t%s\nFROM\n\t%s\n", [
				implode(",\n\t", $select),
				implode(",\n\t", $from),
			]);
			if (($chains = ($wheres = $query->wheres())->chains())->hasChains()) {
				$formatWhere = function (IWhere $where, array $schemas, array $selects, array $params): string {
					$where = $where->toObject();
					switch ($where->type) {
						case 'equalTo':
							if (isset($params[$where->param]) === false) {
								throw new StorageException(sprintf('Missing where parameter [%s]; available parameters [%s].', $where->param, implode(', ', $params)));
							}
							$fragment = vsprintf('%s.%s = :%s', [
								$this->delimit($where->alias),
								$this->delimit($where->property),
								$where->param,
							]);
							$params[$where->param] = $this->filterValue($schemas[$selects[$where->alias]]->getAttribute($where->property), $params[$where->param]);
							unset($params[$where->param]);
							return $fragment;
						case 'in':
							if (isset($params[$where->param]) === false) {
								throw new StorageException(sprintf('Missing where parameter [%s]; available parameters [%s].', $where->param, implode(', ', $params)));
							} else if (is_iterable($params[$where->param]) === false) {
								throw new StorageException(sprintf('Where in parameter [%s] is not an iterable.', $where->param));
							}
							$schema = $schemas[$selects[$where->alias]];
							$attribute = $schema->getAttribute($where->property);
							$this->exec(vsprintf('CREATE TEMPORARY TABLE %s ( item %s )', [
								$temporary = $this->delimit($this->randomService->uuid()),
								$this->type($attribute->getType()),
							]));
							$statement = $this->pdo->prepare(vsprintf('INSERT INTO %s (item) VALUES (:item)', [
								$temporary,
							]));
							foreach ($params[$where->param] as $item) {
								$statement->execute([
									'item' => $this->filterValue($attribute, $item),
								]);
							}
							unset($params[$where->param]);
							return vsprintf('%s.%s IN (SELECT item FROM %s)', [
								$this->delimit($where->alias),
								$this->delimit($where->property),
								$temporary,
							]);
						default:
							throw new StorageException(sprintf('Unsupported where type [%s].', $where->type));
					}
				};
				$fragments = [];
				$formatChain = function (IQuery $query, IChain $chain, callable $formatChain, callable $formatWhere, array &$fragments): void {
					$wheres = $query->wheres();
					$chains = $wheres->chains();
					$fragments[] = '(';
					foreach ($chain as $stdClass) {
						if ($chains->hasChain($stdClass->name)) {
							$fragments[] = $formatChain($query, $chains->getChain($stdClass->name), $formatChain, $formatWhere, $fragments);
							continue;
						}
						$p = $query->getParams();
						$fragments[] = $formatWhere($wheres->getWhere($stdClass->name), $this->getSchemas($query), $query->getSelects(), $p);
						$fragments[] = ' ' . strtoupper($stdClass->operator) . ' ';
					}
					array_pop($fragments);
					$fragments[] = ')';
				};
				$sql .= "WHERE\n\t";
				$formatChain($query, $chains->getChain(), $formatChain, $formatWhere, $fragments) . "\n";
				$sql .= implode('', $fragments);
			}
			if ($count === false && $query->hasOrder() && $orders = $query->getOrders()) {
				$sql .= "ORDER BY\n\t";
				$orderList = [];
				foreach ($orders as $stdClass) {
					$orderList[] = vsprintf('%s.%s %s', [
						$this->delimit($stdClass->alias),
						$this->delimit($stdClass->property),
						in_array($order = strtoupper($stdClass->order), ['ASC', 'DESC']) ? $order : 'ASC',
					]);
				}
				$sql .= implode(" ,\n\t", $orderList) . "\n";
			}
			if ($count === false && $query->hasPage() && $page = $query->getPage()) {
				$sql .= vsprintf('LIMIT %d OFFSET %d', [
					$page->size,
					$page->page * $page->size,
				]);
			}
			return new Command($sql, $params);
		}

		/** @inheritdoc */
		public function create(string $name): IStorage {
			try {
				$schema = $this->schemaManager->getSchema($name);
				$sql = 'CREATE TABLE ' . $this->delimit($table = $schema->getRealName()) . " (\n\t";
				$columns = [];
				$primary = null;
				foreach ($schema->getAttributes() as $attribute) {
					$column = ($fragment = $this->delimit($attribute->getName())) . ' ' . $this->type($attribute->hasSchema() ? $this->schemaManager->getSchema($attribute->getSchema())->getPrimary()->getType() : $attribute->getType());
					if ($attribute->isPrimary()) {
						$primary = $fragment;
					} else if ($attribute->isUnique()) {
						$column .= ' UNIQUE';
					}
					if ($attribute->isRequired()) {
						$column .= ' NOT NULL';
					}
					$columns[] = $column;
				}
				if ($primary) {
					$columns[] = "CONSTRAINT " . $this->delimit(sha1($table . '.primary.' . $primary)) . ' PRIMARY KEY (' . $primary . ')';
				}
				$this->exec($sql . implode(",\n\t", $columns) . "\n)");
				return $this;
			} catch (Throwable $exception) {
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function insert(IEntity $entity): IStorage {
			try {
				$columns = [];
				$params = [];
				foreach ($source = $this->prepareInsert($entity) as $k => $v) {
					$columns[] = $this->delimit($k);
					$params[sha1($k)] = $v;
				}
				$this->fetch(
					"INSERT INTO\n\t" .
					$this->delimit(($schema = $entity->getSchema())->getRealName()) .
					" (\n\t" . implode(",\n\t", $columns) .
					")\n\tVALUES (\n\t:" .
					implode(",\n\t:", array_keys($params)) .
					"\n)\n",
					$params
				);
				$entity->put($this->prepareOutput($schema, $source));
				$entity->commit();
				return $this;
			} catch (Throwable $exception) {
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function update(IEntity $entity): IStorage {
			try {
				$schema = $entity->getSchema();
				$primary = $schema->getPrimary();
				$table = $this->delimit($schema->getRealName());
				$params = ['primary' => $entity->getPrimary()->get()];
				$columns = [];
				foreach ($source = $this->prepareUpdate($entity) as $k => $v) {
					$params[$paramId = sha1($k)] = $v;
					$columns[] = $this->delimit($k) . ' = :' . $paramId;
				}
				$this->fetch(
					"UPDATE\n\t" . $table . "\nSET\n\t" . implode(",\n\t", $columns) . "\nWHERE\n\t" . $this->delimit($primary->getName()) . ' = :primary',
					$params
				);
				$entity->put($this->prepareOutput($schema, $source));
				$entity->commit();
				return $this;
			} catch (Throwable $exception) {
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function save(IEntity $entity): IStorage {
			try {
				$schema = $entity->getSchema();
				$primary = $entity->getPrimary();
				$attribute = $entity->getPrimary()->getAttribute();
				if ($primary->get() === null) {
					return $this->insert($entity);
				}
				$count = ['count' => 0];
				foreach ($this->fetch('SELECT COUNT(' . $this->delimit($attribute->getName()) . ') AS count FROM ' . $this->delimit($schema->getRealName()) . ' WHERE ' . $this->delimit($attribute->getName()) . ' = :primary', ['primary' => $primary->get()]) as $count) {
					break;
				}
				if ($count['count'] === 0) {
					return $this->insert($entity);
				}
				return $this->update($entity);
			} catch (Throwable $exception) {
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function load(string $schema, string $id): IEntity {
			try {
				$schema = $this->schemaManager->getSchema($schema);
				$query = "SELECT * FROM " . $this->delimit($schema->getRealName()) . " WHERE " . $this->delimit($schema->getPrimary()->getName()) . ' = :primary';
				foreach ($this->fetch($query, ['primary' => $id]) as $item) {
					$entity = new Entity($schema);
					$entity->push($this->prepareOutput($schema, (object)$item));
					return $entity;
				}
				throw new EntityNotFoundException(sprintf('Cannot load any entity [%s] with id [%s].', $schema->getName(), $id));
			} catch (EntityNotFoundException $exception) {
				throw $exception;
			} catch (Throwable $exception) {
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function delete(IEntity $entity): IStorage {
			$schema = $entity->getSchema();
			$primary = $entity->getPrimary();
			$this->fetch(
				'DELETE FROM ' . $this->delimit($schema->getRealName()) . ' WHERE ' . $this->delimit($primary->getAttribute()->getName()) . ' = :primary',
				[
					'primary' => $primary->get(),
				]
			);
			return $this;
		}

		/** @inheritdoc */
		public function unlink(IEntity $entity, IEntity $target, string $relation): IStorage {
			$this->checkRelation(
				$relationSchema = $this->schemaManager->getSchema($relation),
				$entity->getSchema(),
				$target->getSchema()
			);
			$this->fetch(
				'DELETE FROM ' . $this->delimit($relationSchema->getRealName()) . ' WHERE ' . $this->delimit($relationSchema->getSource()->getName()) . ' = :a AND ' . $this->delimit($relationSchema->getTarget()->getName()) . ' = :b',
				[
					'a' => $entity->getPrimary()->get(),
					'b' => $target->getPrimary()->get(),
				]
			);
			return $this;
		}

		/** @inheritdoc */
		public function onStart(): void {
			$this->pdo->beginTransaction();
		}

		/** @inheritdoc */
		public function onCommit(): void {
			$this->pdo->commit();
		}

		/** @inheritdoc */
		public function onRollback(): void {
			$this->pdo->rollBack();
		}

		/**
		 * @param Throwable $throwable
		 *
		 * @return Throwable
		 */
		protected function exception(Throwable $throwable): Throwable {
			return $throwable;
		}

		/**
		 * @inheritdoc
		 *
		 * @throws ConfigException
		 */
		public function handleSetup(): void {
			parent::handleSetup();
			$this->pdo = new PDO(
				$this->section->require('dsn'),
				$this->section->require('user'),
				$this->section->optional('password'),
				$this->options
			);
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
			$this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$this->pdo->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_EMPTY_STRING);
			$this->pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
			$this->pdo->setAttribute(PDO::ATTR_TIMEOUT, 120);
		}

		abstract public function delimit(string $delimit): string;

		/**
		 * @param string $type
		 *
		 * @return string
		 *
		 * @throws StorageException
		 */
		abstract public function type(string $type): string;
	}
