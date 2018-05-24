<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Collection\Entity;
	use Edde\Collection\EntityNotFoundException;
	use Edde\Collection\IEntity;
	use Edde\Config\ConfigException;
	use Edde\Filter\FilterException;
	use Edde\Query\ISelectQuery;
	use Edde\Schema\ISchema;
	use Edde\Schema\SchemaException;
	use Edde\Service\Schema\SchemaManager;
	use Generator;
	use PDO;
	use PDOException;
	use Throwable;
	use function array_unique;
	use function array_values;
	use function implode;
	use function sha1;

	abstract class AbstractPdoStorage extends AbstractStorage {
		use SchemaManager;
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
		public function detach(IEntity $entity, IEntity $target, string $relation): IStorage {
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
		 * @param ISelectQuery $selectQuery
		 *
		 * @return Generator
		 *
		 * @throws SchemaException
		 * @throws StorageException
		 * @throws FilterException
		 */
		protected function executeSelect(ISelectQuery $selectQuery): Generator {
			$query = "SELECT\n\t";
			$params = [];
			$uses = $selectQuery->getSchemas();
			/** @var $schemas ISchema[] */
			$schemas = [];
			foreach (array_unique(array_values($uses)) as $schema) {
				$schemas[$schema] = $this->schemaManager->getSchema($schema);
			}
			$select = [];
			$from = [];
			foreach ($uses as $alias => $schema) {
				foreach ($schemas[$schema]->getAttributes() as $name => $attribute) {
					$select[] = $this->delimit($alias) . '.' . $this->delimit($name) . ' AS ' . $this->delimit($alias . '.' . $name);
				}
				$from[] = $this->delimit($schemas[$schema]->getRealName()) . ' ' . $this->delimit($alias);
			}
			$query .= implode(",\n\t", $select) . "\nFROM\n\t" . implode(",\n\t", $from) . "\n";
			foreach ($this->fetch($query, $params) as $row) {
				yield $this->row($row, $schemas, $uses);
			}
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
