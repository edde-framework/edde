<?php
	declare(strict_types=1);
	namespace Edde\Storage;

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
				$items = [];
				foreach ($row as $k => $v) {
					[$alias, $property] = explode('.', $k, 2);
					$items[$alias] = $items[$alias] ?? new stdClass();
					$items[$alias]->$property = $v;
				}
				foreach ($items as $alias => $item) {
					$items[$alias] = $this->prepareOutput($schemas[$uses[$alias]], $item);
				}
				yield new Row($items);
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
				return $source;
			} catch (Throwable $exception) {
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function update(string $schema, stdClass $source): stdClass {
			try {
				$schema = $this->schemaManager->getSchema($schema);
				$table = $this->delimit($schema->getRealName());
				$params = [];
				$columns = [];
				foreach ($source = $this->prepareInput($schema, $source) as $k => $v) {
					$params[$paramId = sha1($k)] = $v;
					$columns[] = ':' . $paramId . '=' . $this->delimit($k);
				}
				$query = "UPDATE\n\t" . $table . "\n";
				$query .= "SET\n\t" . implode(",\n\t", $columns);
				$query .= "WHERE\nblabla";
				$this->fetch($query, $params);
				return $source;
			} catch (Throwable $exception) {
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function load(string $name, string $id): stdClass {
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
