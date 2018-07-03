<?php
	declare(strict_types=1);

	namespace Edde\Ext\Database\Sqlite;

	use Edde\Api\Container\Inject\Container;
	use Edde\Api\Database\Exception\DriverException;
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
	class Driver extends AbstractDriver {
		use Container;
		/**
		 * @var PDO
		 */
		public $pdo;
		/**
		 * @var IStaticQueryFactory
		 */
		protected $staticQueryFactory;

		/**
		 * @inheritdoc
		 */
		public function start(bool $exclusive = false) {
			$this->pdo->beginTransaction();
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function commit() {
			$this->pdo->commit();
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function rollback() {
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
			return $this->pdo->quote($quote);
		}

		/**
		 * @inheritdoc
		 * @throws DriverException
		 */
		public function type(string $type): string {
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
			return $this->native($this->staticQueryFactory->create($query));
		}

		/**
		 * @inheritdoc
		 * @throws UnknownSourceException
		 * @throws UniqueException
		 * @throws \PDOException
		 */
		public function native(IStaticQuery $staticQuery) {
			try {
				$statement = $this->pdo->prepare($staticQuery->getQuery());
				$statement->setFetchMode(PDO::FETCH_ASSOC);
				$statement->execute($staticQuery->getParameterList());
				return $statement;
			} catch (\PDOException $exception) {
				if (strpos($message = $exception->getMessage(), 'no such table') !== false) {
					throw new UnknownSourceException($exception->getMessage(), 0, $exception);
				} else if (strpos($message, 'UNIQUE constraint failed') !== false) {
					throw new UniqueException($exception->getMessage(), 0, $exception);
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
		 * @throws \Edde\Api\Database\Exception\DriverException
		 */
		protected function handleInit() {
			parent::handleInit();
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
			$this->container->setup();
			$this->staticQueryFactory = $this->container->create(QueryFactory::class, [$this], __METHOD__);
		}

		/**
		 * @throws DriverException
		 */
		protected function handleSetup() {
			parent::handleSetup();
			if (extension_loaded('pdo_sqlite') === false) {
				throw new DriverException('Sqlite PDO is not available, oops!');
			}
			$this->pdo = new PDO($this->dsn->getDsn());
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
			$this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$this->pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
			$this->pdo->setAttribute(PDO::ATTR_TIMEOUT, 120);
		}
	}
