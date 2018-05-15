<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Config\ConfigException;
	use Edde\Filter\FilterException;
	use Edde\Query\INative;
	use Edde\Query\ISelectQuery;
	use Edde\Schema\ISchema;
	use Edde\Service\Filter\FilterManager;
	use Edde\Service\Schema\SchemaFilterService;
	use Edde\Service\Schema\SchemaManager;
	use Edde\Service\Schema\SchemaValidatorService;
	use PDO;
	use PDOException;
	use stdClass;
	use Throwable;
	use function implode;

	abstract class AbstractPdoStorage extends AbstractStorage {
		use SchemaManager;
		use SchemaFilterService;
		use SchemaValidatorService;
		use FilterManager;
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

		/**
		 * @param ISchema  $schema
		 * @param stdClass $stdClass
		 *
		 * @return stdClass
		 * @throws FilterException
		 */
		protected function prepareInput(ISchema $schema, stdClass $stdClass): stdClass {
			$stdClass = clone $stdClass;
			foreach ($schema->getAttributes() as $name => $attribute) {
				/**
				 * if there is a generator and property does not exists, generate a new value; property should not exists to
				 * accept NULL and empty values as generated value
				 */
				if (($generator = $attribute->getFilter('generator')) && (property_exists($stdClass, $name) === false)) {
					$stdClass->$name = $this->filterManager->getFilter('storage:' . $generator)->input(null);
				}
				/**
				 * default value will provide default all the times, thus from this point it's safe to use $stdClass->$name
				 */
				if (property_exists($stdClass, $name) === false) {
					$stdClass->$name = $attribute->getDefault();
				}
				if ($filter = $attribute->getFilter('type')) {
					$stdClass->$name = $this->filterManager->getFilter('storage:' . $filter)->input($stdClass->$name);
				}
				/**
				 * common filter support; filter name is used for both directions
				 */
				if ($filter = $attribute->getFilter('filter')) {
					$stdClass->$name = $this->filterManager->getFilter('storage:' . $filter)->input($stdClass->$name);
				}
			}
			return $stdClass;
		}

		protected function prepareOutput(ISchema $schema, stdClass $stdClass): stdClass {
			$stdClass = clone $stdClass;
			foreach ($schema->getAttributes() as $name => $attribute) {
				if (property_exists($stdClass, $name) === false) {
					$stdClass->$name = $attribute->getDefault();
				}
				if ($filter = $attribute->getFilter('type')) {
					$stdClass->$name = $this->filterManager->getFilter('storage:' . $filter)->output($stdClass->$name);
				}
				if ($filter = $attribute->getFilter('filter')) {
					$stdClass->$name = $this->filterManager->getFilter('storage:' . $filter)->output($stdClass->$name);
				}
			}
			return $stdClass;
		}

		/** @inheritdoc */
		public function insert(string $schema, stdClass $source): stdClass {
			try {
				$schema = $this->schemaManager->getSchema($schema);
				$table = $this->delimit($schema->getRealName());
				$columns = [];
				$params = [];
				/**
				 * first validation of the pure input to see if values being filtered
				 * are correct...
				 */
				$this->schemaValidatorService->validate(
					$schema,
					$source,
					'storage:entity'
				);
				/**
				 * actual filter from PHP side to storage side (thus input to storage)
				 */
				$source = $this->schemaFilterService->input($schema, $source, 'storage');
				/**
				 * and validation of filtered values prepared for storage to see if filter
				 * provided expected data to be stored
				 */
				$this->schemaValidatorService->validate(
					$schema,
					$source,
					'storage'
				);
				foreach ($source as $k => $v) {
					$columns[] = $this->delimit($k);
					$params[$paramId = sha1($k)] = $v;
				}
				$sql = 'INSERT INTO ' . $table . ' (' . implode(', ', $columns) . ') VALUES (:' . implode(', :', array_keys($params)) . ')';
				$this->fetch($sql, $params);
				return $source;
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
