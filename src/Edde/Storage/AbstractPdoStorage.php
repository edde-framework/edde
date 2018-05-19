<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Collection\EntityNotFoundException;
	use Edde\Config\ConfigException;
	use Edde\Filter\FilterException;
	use Edde\Query\ISelectQuery;
	use Edde\Schema\ISchema;
	use Edde\Schema\SchemaException;
	use Edde\Service\Schema\SchemaManager;
	use Generator;
	use PDO;
	use PDOException;
	use stdClass;
	use Throwable;
	use function array_unique;
	use function array_values;
	use function implode;
	use function property_exists;
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
			$froms = [];
			foreach ($uses as $alias => $schema) {
				foreach ($schemas[$schema]->getAttributes() as $name => $attribute) {
					$select[] = $this->delimit($alias) . '.' . $this->delimit($name) . ' AS ' . $this->delimit($alias . '.' . $name);
				}
				$froms[] = $this->delimit($schemas[$schema]->getRealName()) . ' ' . $this->delimit($alias);
			}
			$query .= implode(",\n\t", $select) . "\n";
			$query .= "FROM\n\t" . implode(",\n\t", $froms) . "\n";
			foreach ($this->fetch($query, $params) as $row) {
				yield $this->row($row, $schemas, $uses);
			}
		}

		/** @inheritdoc */
		public function create(string $name): IStorage {
			try {
				$schema = $this->schemaManager->getSchema($name);
				$sql = 'CREATE TABLE ' . $this->delimit($table = $schema->getRealName()) . " (\n\t";
				$columns = [];
				$primary = null;
				foreach ($schema->getAttributes() as $property) {
					$column = ($fragment = $this->delimit($property->getName())) . ' ' . $this->type($property->getType());
					if ($property->isPrimary()) {
						$primary = $fragment;
					} else if ($property->isUnique()) {
						$column .= ' UNIQUE';
					}
					if ($property->isRequired()) {
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
		public function insert(string $schema, stdClass $source): stdClass {
			try {
				$schema = $this->schemaManager->getSchema($schema);
				$columns = [];
				$params = [];
				foreach ($source = $this->prepareInput($schema, $source) as $k => $v) {
					$columns[] = $this->delimit($k);
					$params[sha1($k)] = $v;
				}
				$this->fetch(
					"INSERT INTO\n\t" .
					$this->delimit($schema->getRealName()) .
					" (\n\t" . implode(",\n\t", $columns) .
					")\n\tVALUES (\n\t:" .
					implode(",\n\t:", array_keys($params)) .
					"\n)\n",
					$params
				);
				return $this->prepareOutput($schema, $source);
			} catch (Throwable $exception) {
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function update(string $schema, stdClass $source): stdClass {
			try {
				$schema = $this->schemaManager->getSchema($schema);
				$primary = $schema->getPrimary();
				$table = $this->delimit($schema->getRealName());
				$params = [
					'primary' => $source->{$primary->getName()},
				];
				$columns = [];
				foreach ($source = $this->prepareInput($schema, $source, true) as $k => $v) {
					$params[$paramId = sha1($k)] = $v;
					$columns[] = $this->delimit($k) . ' = :' . $paramId;
				}
				$this->fetch(
					"UPDATE\n\t" . $table . "\nSET\n\t" . implode(",\n\t", $columns) . "\nWHERE\n\t" . $this->delimit($primary->getName()) . ' = :primary',
					$params
				);
				return $this->prepareOutput($schema, $source);
			} catch (Throwable $exception) {
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function save(string $schema, stdClass $source): stdClass {
			try {
				$schema = $this->schemaManager->getSchema($schema);
				$primary = $schema->getPrimary()->getName();
				if (property_exists($source, $primary) === false || $source->$primary === null) {
					return $this->insert($schema->getName(), $source);
				}
				$count = ['count' => 0];
				foreach ($this->fetch('SELECT COUNT(' . $this->delimit($primary) . ') AS count FROM ' . $this->delimit($schema->getRealName())) as $count) {
					break;
				}
				if ($count['count'] === 0) {
					return $this->insert($schema->getName(), $source);
				}
				return $this->update($schema->getName(), $source);
			} catch (Throwable $exception) {
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function load(string $schema, string $id): stdClass {
			try {
				$schema = $this->schemaManager->getSchema($schema);
				$query = "SELECT * FROM " . $this->delimit($schema->getRealName()) . " WHERE " . $this->delimit($schema->getPrimary()->getName()) . ' = :primary';
				foreach ($this->fetch($query, ['primary' => $id]) as $item) {
					return $this->prepareOutput($schema, (object)$item);
				}
				throw new EntityNotFoundException(sprintf('Cannot load any entity [%s] with id [%s].', $schema, $id));
			} catch (EntityNotFoundException $exception) {
				throw $exception;
			} catch (Throwable $exception) {
				throw $this->exception($exception);
			}
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
