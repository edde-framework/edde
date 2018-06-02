<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Collection\Entity;
	use Edde\Collection\EntityNotFoundException;
	use Edde\Collection\IEntity;
	use Edde\Config\ConfigException;
	use Edde\Query\IQuery;
	use Edde\Service\Schema\SchemaManager;
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
		/** @var PDO */
		protected $pdo;

		/** @inheritdoc */
		public function fetch($query, array $params = []) {
			try {
				$statement = $this->pdo->prepare($query);
				$statement->setFetchMode(PDO::FETCH_ASSOC);
				$statement->execute($params);
				return $statement;
			} catch (PDOException $exception) {
				/** @noinspection PhpUnhandledExceptionInspection */
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
				/** @noinspection PhpUnhandledExceptionInspection */
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function query(IQuery $query, array $binds = []): Generator {
			$schemas = $this->schemaManager->getSchemas($query->getSchemas());
			$selects = $query->getSelects();
			$params = [];
			foreach ($query->params($binds) as $name => $param) {
				$hash = $param->getHash();
				if (is_iterable($value = $param->getValue()) === false) {
					$params[$hash] = $param->getValue();
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
					$temporary = $this->compiler->delimit($hash),
					$this->type($schemas[$selects[$param->getAlias()]]->getAttribute($param->getProperty())->getType()),
				]));
				$statement = $this->pdo->prepare(vsprintf('INSERT INTO %s (item) VALUES (:item)', [
					$temporary,
				]));
				foreach ($value as $v) {
					$statement->execute([
						'item' => $v,
					]);
				}
			}
			foreach ($this->fetch($this->compiler->compile($query), $params) as $row) {
				yield $this->row($row, $schemas, $selects);
			}
		}

		/** @inheritdoc */
		public function create(string $name): IStorage {
			try {
				$schema = $this->schemaManager->getSchema($name);
				$table = $schema->getRealName();
				$columns = [];
				$primary = null;
				foreach ($schema->getAttributes() as $attribute) {
					$column = vsprintf('%s %s', [
						$fragment = $this->compiler->delimit($attribute->getName()),
						$this->type(
							$attribute->hasSchema() ?
								$this->schemaManager->getSchema($attribute->getSchema())->getPrimary()->getType() :
								$attribute->getType()
						),
					]);
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
					$columns[] = vsprintf('CONSTRAINT %s PRIMARY KEY (%s)', [
						$this->compiler->delimit(sha1($table . '.primary.' . $primary)),
						$primary,
					]);
				}
				$this->exec(vsprintf("CREATE TABLE %s (\n\t%s\n)", [
					$this->compiler->delimit($table),
					implode(",\n\t", $columns),
				]));
				return $this;
			} catch (Throwable $exception) {
				/** @noinspection PhpUnhandledExceptionInspection */
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function insert(IEntity $entity): IStorage {
			try {
				$columns = [];
				$params = [];
				foreach ($source = $this->storageFilterService->input($entity->getSchema(), $entity->toObject()) as $k => $v) {
					$columns[sha1($k)] = $this->compiler->delimit($k);
					$params[sha1($k)] = $v;
				}
				$this->fetch(
					vsprintf('INSERT INTO %s (%s) VALUES (:%s)', [
						$this->compiler->delimit(($schema = $entity->getSchema())->getRealName()),
						implode(',', $columns),
						implode(',:', array_keys($params)),
					]),
					$params
				);
				$entity->put($this->storageFilterService->output($schema, $source));
				$entity->commit();
				return $this;
			} catch (Throwable $exception) {
				/** @noinspection PhpUnhandledExceptionInspection */
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function update(IEntity $entity): IStorage {
			try {
				$schema = $entity->getSchema();
				$primary = $schema->getPrimary();
				$table = $this->compiler->delimit($schema->getRealName());
				$params = ['primary' => $entity->getPrimary()->get()];
				$columns = [];
				foreach ($source = $this->storageFilterService->update($entity->getSchema(), $entity->toObject()) as $k => $v) {
					$columns[] = $this->compiler->delimit($k) . ' = :' . ($paramId = sha1($k));
					$params[$paramId] = $v;
				}
				$this->fetch(
					vsprintf('UPDATE %s SET %s WHERE %s = :primary', [
						$table,
						implode(', ', $columns),
						$this->compiler->delimit($primary->getName()),
					]),
					$params
				);
				$entity->put($this->storageFilterService->output($schema, $source));
				$entity->commit();
				return $this;
			} catch (Throwable $exception) {
				/** @noinspection PhpUnhandledExceptionInspection */
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
				$params = [
					$this->compiler->delimit($attribute->getName()),
					$this->compiler->delimit($schema->getRealName()),
					$this->compiler->delimit($attribute->getName()),
				];
				$count = ['count' => 0];
				foreach ($this->fetch(vsprintf('SELECT COUNT(%s) AS count FROM %s WHERE %s = :primary', $params), ['primary' => $primary->get()]) as $count) {
					break;
				}
				if ($count['count'] === 0) {
					return $this->insert($entity);
				}
				return $this->update($entity);
			} catch (Throwable $exception) {
				/** @noinspection PhpUnhandledExceptionInspection */
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function load(string $schema, string $id): IEntity {
			try {
				$schema = $this->schemaManager->getSchema($schema);
				$params = [
					$this->compiler->delimit($schema->getRealName()),
					$this->compiler->delimit($schema->getPrimary()->getName()),
				];
				foreach ($this->fetch(vsprintf('SELECT * FROM %s WHERE %s = :primary', $params), ['primary' => $id]) as $item) {
					$entity = new Entity($schema);
					$entity->push($this->storageFilterService->output($schema, (object)$item));
					return $entity;
				}
				throw new EntityNotFoundException(sprintf('Cannot load any entity [%s] with id [%s].', $schema->getName(), $id));
			} catch (EntityNotFoundException $exception) {
				throw $exception;
			} catch (Throwable $exception) {
				/** @noinspection PhpUnhandledExceptionInspection */
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function delete(IEntity $entity): IStorage {
			$schema = $entity->getSchema();
			$primary = $entity->getPrimary();
			$this->fetch(
				vsprintf('DELETE FROM %s WHERE %s = :primary', [
					$this->compiler->delimit($schema->getRealName()),
					$this->compiler->delimit($primary->getAttribute()->getName()),
				]),
				[
					'primary' => $primary->get(),
				]
			);
			return $this;
		}

		/** @inheritdoc */
		public function unlink(IEntity $entity, IEntity $target, string $relation): IStorage {
			($relationSchema = $this->schemaManager->getSchema($relation))->checkRelation(
				$entity->getSchema(),
				$target->getSchema()
			);
			$this->fetch(
				vsprintf('DELETE FROM %s WHERE %s = :a AND %s = :b', [
					$this->compiler->delimit($relationSchema->getRealName()),
					$this->compiler->delimit($relationSchema->getSource()->getName()),
					$this->compiler->delimit($relationSchema->getTarget()->getName()),
				]),
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
				$this->section->optional('password')
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
