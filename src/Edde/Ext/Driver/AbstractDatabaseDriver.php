<?php
	declare(strict_types=1);
	namespace Edde\Ext\Driver;

		use Edde\Api\Driver\Exception\DriverException;
		use Edde\Api\Driver\IDriver;
		use Edde\Api\Query\ICrateSchemaQuery;
		use Edde\Common\Driver\AbstractDriver;
		use PDO;

		abstract class AbstractDatabaseDriver extends AbstractDriver {
			/**
			 * @var array
			 */
			protected $dsn;
			/**
			 * @var PDO
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
			public function native($query, array $params = []) {
				$exception = null;
				try {
					$statement = $this->pdo->prepare($query);
					$statement->setFetchMode(PDO::FETCH_ASSOC);
					$statement->execute($params);
					return $statement;
				} catch (\PDOException $exception) {
					throw $this->exception($exception);
				}
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

			protected function exception(\Throwable $throwable): \Throwable {
				return new DriverException('Unhandled exception: ' . $throwable->getMessage(), 0, $throwable);
			}

			/**
			 * @param ICrateSchemaQuery $crateSchemaQuery
			 *
			 * @throws \Throwable
			 */
			protected function executeCreateSchemaQuery(ICrateSchemaQuery $crateSchemaQuery) {
				$schema = $crateSchemaQuery->getSchema();
				$sql = 'CREATE TABLE ' . ($this->delimite($table = $schema->getRealName())) . " (\n\t";
				$columnList = [];
				$primaryList = [];
				foreach ($schema->getPropertyList() as $property) {
					$column = ($name = $this->delimite($property->getName())) . ' ' . $this->type($property->getType());
					if ($property->isPrimary()) {
						$primaryList[] = $name;
					} else if ($property->isUnique()) {
						$column .= ' UNIQUE';
					}
					if ($property->isRequired()) {
						$column .= ' NOT NULL';
					}
					$columnList[] = $column;
				}
				if (empty($primaryList) === false) {
					$columnList[] = "CONSTRAINT " . $this->delimite(sha1($table . '_primary_' . $primary = implode(', ', $primaryList))) . ' PRIMARY KEY (' . $primary . ')';
				}
				$sql .= implode(",\n\t", $columnList);
				foreach ($schema->getLinks() as $link) {
					$sql .= ",\n\tFOREIGN KEY (" . $this->delimite($link->getFrom()->getName()) . ') REFERENCES ' . $this->delimite($link->getTo()->getRealName()) . '(' . $this->delimite($link->getTo()->getName()) . ') ON DELETE RESTRICT ON UPDATE RESTRICT';
				}
				$this->native($sql . "\n)");
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

			abstract public function delimite(string $delimite): string;

			abstract public function type(string $type): string;
		}
