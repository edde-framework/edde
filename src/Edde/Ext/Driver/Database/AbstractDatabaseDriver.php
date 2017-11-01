<?php
	namespace Edde\Ext\Driver\Database;

		use Edde\Api\Driver\Exception\DriverException;
		use Edde\Api\Driver\IDriver;
		use Edde\Api\Entity\IEntity;
		use Edde\Api\Query\INativeQuery;
		use Edde\Common\Driver\AbstractDriver;
		use PDO;

		abstract class AbstractDatabaseDriver extends AbstractDriver {
			/**
			 * @var array
			 */
			protected $dsn;
			/**
			 * @var \PDO
			 */
			protected $pdo;

			public function __construct(string $dsn, string $user = null, string $password = null) {
				$this->dsn = array_filter([
					$dsn,
					$user,
					$password,
				]);
			}

			/**
			 * @inheritdoc
			 */
			public function execute(INativeQuery $nativeQuery) {
				$exception = null;
				try {
					$statement = $this->pdo->prepare($nativeQuery->getQuery());
					$statement->setFetchMode(PDO::FETCH_ASSOC);
					$statement->execute($nativeQuery->getParameterList());
					return $statement;
				} catch (\PDOException $exception) {
					$this->exception($exception);
				}
				throw new DriverException('Unhandled exception: ' . $exception->getMessage(), 0, $exception);
			}

			/**
			 * @inheritdoc
			 */
			public function start(): IDriver {
				$this->pdo->beginTransaction();
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function commit(): IDriver {
				$this->pdo->commit();
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function rollback(): IDriver {
				$this->pdo->rollBack();
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function toArray(IEntity $entity): array {
				$source = [];
				foreach ($entity->getDirtyProperties() as $property) {
					$source[$property->getName()] = $property->get();
				}
				return $source;
			}

			protected function exception(\Throwable $throwable) {
			}

			public function handleSetup(): void {
				parent::handleSetup();
				try {
					$this->pdo = new PDO(...$this->dsn);
					$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					$this->pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
					$this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
					$this->pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
					$this->pdo->setAttribute(PDO::ATTR_TIMEOUT, 120);
				} finally {
					/**
					 * prevent credentials to somehow throw up to the user
					 */
					$this->dsn = null;
				}
			}
		}
