<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Collection\Entity;
	use Edde\Collection\EntityNotFoundException;
	use Edde\Collection\IEntity;
	use Edde\Config\ConfigException;
	use Edde\Query\IQuery;
	use Edde\Service\Container\Container;
	use Edde\Service\Schema\SchemaManager;
	use Edde\Service\Security\RandomService;
	use Generator;
	use PDO;
	use PDOException;
	use Throwable;
	use function implode;
	use function is_iterable;
	use function sha1;
	use function sprintf;
	use function vsprintf;

	abstract class AbstractPdoStorage extends AbstractStorage {
		use SchemaManager;
		use RandomService;
		use Container;
		/** @var string */
		protected $delimiter;
		/** @var array */
		protected $options;
		/** @var PDO */
		protected $pdo;
		/** @var ICompiler */
		protected $compiler;

		/** @inheritdoc */
		public function __construct(string $config, string $delimiter, array $options = []) {
			parent::__construct($config);
			$this->delimiter = $delimiter;
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
		public function query(IQuery $query, array $binds = []): Generator {
			$compiler = $this->compiler();
			$schemas = $this->getSchemas($query);
			$selects = $query->getSelects();
			$params = [];
			foreach ($query->binds($binds) as $name => $bind) {
				$param = $bind->getParam();
				$hash = $param->getHash();
				$schema = $schemas[$selects[$param->getAlias()]];
				$attribute = $schema->getAttribute($param->getProperty());
				if (is_iterable($value = $bind->getValue()) === false) {
					$params[$hash] = $this->filterValue($attribute, $bind->getValue());
					continue;
				}
				/**
				 * this is ugly hack, because of some motherfucker who not implement array support for
				 * WHERE IN clause; so in general it was much more easier to use param name as a temporary
				 * table name from which WHERE IN makes sub-query
				 *
				 * it's not necessary to thanks me
				 */
				$this->exec(vsprintf('CREATE TEMPORARY TABLE %s ( item %s )', [
					$temporary = $compiler->delimit($hash),
					$this->type($attribute->getType()),
				]));
				$statement = $this->pdo->prepare(vsprintf('INSERT INTO %s (item) VALUES (:item)', [
					$temporary,
				]));
				foreach ($value as $v) {
					$statement->execute([
						'item' => $this->filterValue($attribute, $v),
					]);
				}
			}
			foreach ($this->fetch($compiler->compile($query), $params) as $row) {
				yield $this->row($row, $schemas, $selects);
			}
		}

		/** @inheritdoc */
		public function compiler(): ICompiler {
			return $this->compiler ?: $this->compiler = $this->container->create(PdoCompiler::class, [$this->delimiter], __METHOD__);
		}

		/** @inheritdoc */
		public function create(string $name): IStorage {
			try {
				$schema = $this->schemaManager->getSchema($name);
				$compiler = $this->compiler();
				$sql = 'CREATE TABLE ' . $compiler->delimit($table = $schema->getRealName()) . " (\n\t";
				$columns = [];
				$primary = null;
				foreach ($schema->getAttributes() as $attribute) {
					$column = ($fragment = $compiler->delimit($attribute->getName())) . ' ' . $this->type($attribute->hasSchema() ? $this->schemaManager->getSchema($attribute->getSchema())->getPrimary()->getType() : $attribute->getType());
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
					$columns[] = "CONSTRAINT " . $compiler->delimit(sha1($table . '.primary.' . $primary)) . ' PRIMARY KEY (' . $primary . ')';
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
				$compiler = $this->compiler();
				foreach ($source = $this->prepareInsert($entity) as $k => $v) {
					$columns[] = $compiler->delimit($k);
					$params[sha1($k)] = $v;
				}
				$this->fetch(
					"INSERT INTO\n\t" .
					$compiler->delimit(($schema = $entity->getSchema())->getRealName()) .
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
				$compiler = $this->compiler();
				$table = $compiler->delimit($schema->getRealName());
				$params = ['primary' => $entity->getPrimary()->get()];
				$columns = [];
				foreach ($source = $this->prepareUpdate($entity) as $k => $v) {
					$params[$paramId = sha1($k)] = $v;
					$columns[] = $compiler->delimit($k) . ' = :' . $paramId;
				}
				$this->fetch(
					"UPDATE\n\t" . $table . "\nSET\n\t" . implode(",\n\t", $columns) . "\nWHERE\n\t" . $compiler->delimit($primary->getName()) . ' = :primary',
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
				$compiler = $this->compiler();
				foreach ($this->fetch('SELECT COUNT(' . $compiler->delimit($attribute->getName()) . ') AS count FROM ' . $compiler->delimit($schema->getRealName()) . ' WHERE ' . $compiler->delimit($attribute->getName()) . ' = :primary', ['primary' => $primary->get()]) as $count) {
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
				$compiler = $this->compiler();
				$query = "SELECT * FROM " . $compiler->delimit($schema->getRealName()) . " WHERE " . $compiler->delimit($schema->getPrimary()->getName()) . ' = :primary';
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
			$compiler = $this->compiler();
			$this->fetch(
				'DELETE FROM ' . $compiler->delimit($schema->getRealName()) . ' WHERE ' . $compiler->delimit($primary->getAttribute()->getName()) . ' = :primary',
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
			$compiler = $this->compiler();
			$this->fetch(
				'DELETE FROM ' . $compiler->delimit($relationSchema->getRealName()) . ' WHERE ' . $compiler->delimit($relationSchema->getSource()->getName()) . ' = :a AND ' . $compiler->delimit($relationSchema->getTarget()->getName()) . ' = :b',
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

		/**
		 * @param string $type
		 *
		 * @return string
		 *
		 * @throws StorageException
		 */
		abstract public function type(string $type): string;
	}
