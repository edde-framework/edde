<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Config\ConfigException;
	use Edde\Query\INative;
	use Edde\Query\ISelectQuery;
	use Edde\Service\Schema\SchemaManager;
	use PDO;
	use PDOException;
	use stdClass;
	use Throwable;
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

		/**
		 * @inheritdoc
		 *
		 * @throws Throwable
		 */
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

		/**
		 * @inheritdoc
		 *
		 * @throws Throwable
		 */
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

		protected function executeSelect(ISelectQuery $selectQuery): INative {
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
				$table = $this->delimit($schema->getRealName());
				$columns = [];
				$params = [];
				foreach ($source as $k => $v) {
					$columns[] = $this->delimit($k);
					$params[$paramId = sha1($k)] = $v;
				}
				$sql = 'INSERT INTO ' . $table . ' (' . implode(', ', $columns) . ') VALUES (:' . implode(', :', array_keys($params)) . ')';
				$this->fetch($sql, $params);
				return $generated;
			} catch (Throwable $exception) {
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function update(string $schema, stdClass $source): IStorage {
			return $this;
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
