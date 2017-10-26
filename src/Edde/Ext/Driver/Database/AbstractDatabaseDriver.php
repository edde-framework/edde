<?php
	namespace Edde\Ext\Driver\Database;

		use Edde\Api\Driver\Exception\DriverException;
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
			public function native(INativeQuery $nativeQuery): \PDOStatement {
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
		}
