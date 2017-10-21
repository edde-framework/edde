<?php
	namespace Edde\Common\Database;

		use Edde\Api\Database\Exception\DriverException;
		use Edde\Api\Query\INativeQuery;
		use PDO;

		/**
		 * Separate PDO driver is because of classic databases (like SQLite or Postgres).
		 */
		abstract class AbstractPdoDriver extends AbstractDriver {
			/**
			 * @var string
			 */
			protected $dsn;
			/**
			 * @var \PDO
			 */
			protected $pdo;

			public function __construct(string $dsn) {
				$this->dsn = $dsn;
			}

			/**
			 * @inheritdoc
			 */
			public function native(INativeQuery $nativeQuery) {
				$exception = null;
				try {
					$prepared = $this->pdo->prepare($nativeQuery->getQuery());
					$prepared->execute($nativeQuery->getParameterList());
					return $prepared;
				} catch (\PDOException $exception) {
					$this->exception($exception);
				}
				throw new DriverException('Unhandled exception.', 0, $exception);
			}

			/**
			 * @inheritdoc
			 */
			public function quote(string $quote): string {
				return $this->pdo->quote($quote);
			}

			protected function exception(\Throwable $throwable) {
			}

			public function handleSetup(): void {
				parent::handleSetup();
				$this->pdo = new PDO($this->dsn);
				$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$this->pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
				$this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				$this->pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
				$this->pdo->setAttribute(PDO::ATTR_TIMEOUT, 120);
				$this->initNativeQueryBuilder();
			}
		}
