<?php
	declare(strict_types=1);

	namespace Edde\Ext\Database\Sqlite;

	use Edde\Api\Database\DriverException;
	use Edde\Api\Query\IQuery;
	use Edde\Api\Query\IStaticQuery;
	use Edde\Api\Query\IStaticQueryFactory;
	use Edde\Common\Database\AbstractDriver;
	use Edde\Common\Storage\UniqueException;
	use Edde\Common\Storage\UnknownSourceException;
	use PDO;

	/**
	 * Sqlite database support.
	 */
	class SqliteDriver extends AbstractDriver {
		/**
		 * @var PDO
		 */
		public $pdo;
		/**
		 * @var string
		 */
		protected $dsn;
		/**
		 * @var IStaticQueryFactory
		 */
		protected $staticQueryFactory;

		/**
		 * @param string $dsn
		 */
		public function __construct(string $dsn) {
			$this->dsn = $dsn;
		}

		/**
		 * @inheritdoc
		 */
		public function start(bool $exclusive = false) {
			$this->use();
			$this->pdo->beginTransaction();
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function commit() {
			$this->use();
			$this->pdo->commit();
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function rollback() {
			$this->use();
			$this->pdo->rollBack();
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function delimite(string $delimite): string {
			return '"' . str_replace('"', '""', $delimite) . '"';
		}

		/**
		 * @inheritdoc
		 */
		public function quote(string $quote): string {
			$this->use();
			return $this->pdo->quote($quote);
		}

		/**
		 * @inheritdoc
		 * @throws DriverException
		 */
		public function type(string $type): string {
			$this->use();
			if (isset($this->typeList[$type]) === false) {
				throw new DriverException(sprintf('Unknown type [%s] for driver [%s].', $type, static::class));
			}
			return $this->typeList[$type];
		}

		/**
		 * @inheritdoc
		 * @throws UniqueException
		 * @throws UnknownSourceException
		 * @throws \PDOException
		 */
		public function execute(IQuery $query): \PDOStatement {
			$this->use();
			return $this->native($this->staticQueryFactory->create($query));
		}

		/**
		 * @inheritdoc
		 * @throws UnknownSourceException
		 * @throws UniqueException
		 * @throws \PDOException
		 */
		public function native(IStaticQuery $staticQuery) {
			$this->use();
			try {
				$statement = $this->pdo->prepare($staticQuery->getQuery());
				$statement->setFetchMode(PDO::FETCH_ASSOC);
				$statement->execute($staticQuery->getParameterList());
				return $statement;
			} catch (\PDOException $exception) {
				if (strpos($message = $exception->getMessage(), 'no such table') !== false) {
					throw new UnknownSourceException($message, 0, $exception);
				} else if (strpos($message, 'UNIQUE constraint failed') !== false) {
					throw new UniqueException($message, 0, $exception);
				} else if (strpos($message, 'General error: 5 database is locked') !== false) {
					throw new LockedDatabaseException($message, 0, $exception);
				}
				throw $exception;
			}
		}

		/**
		 * close this sqlite connection
		 *
		 * @return $this
		 */
		public function close() {
			$this->pdo = null;
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws DriverException
		 */
		protected function prepare() {
			if (extension_loaded('pdo_sqlite') === false) {
				throw new DriverException('Sqlite PDO is not available, oops!');
			}
			$this->pdo = new PDO($this->dsn);
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
			$this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$this->pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
			$this->setTypeList([
				null => 'TEXT',
				'int' => 'INTEGER',
				'bool' => 'INTEGER',
				'float' => 'FLOAT',
				'long' => 'INTEGER',
				'string' => 'TEXT',
				'text' => 'TEXT',
				'datetime' => 'TIMESTAMP',
			]);
			$this->pdo->exec('PRAGMA journal_mode=WAL;');
			$this->staticQueryFactory = new SqliteQueryFactory($this);
		}
	}
