<?php
	declare(strict_types=1);
	namespace Edde\Ext\Driver;

		use Edde\Api\Driver\Exception\DriverException;
		use Edde\Api\Driver\Exception\DriverQueryException;
		use Edde\Api\Driver\IDriver;
		use Edde\Api\Query\Fragment\IWhereTo;
		use Edde\Api\Query\ICrateSchemaQuery;
		use Edde\Api\Query\ICreateRelationQuery;
		use Edde\Api\Query\IInsertQuery;
		use Edde\Api\Query\INativeQuery;
		use Edde\Api\Query\ISelectQuery;
		use Edde\Api\Query\IUpdateQuery;
		use Edde\Common\Driver\AbstractDriver;
		use Edde\Common\Query\InsertQuery;
		use Edde\Common\Query\NativeQuery;
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
			public function native($query, array $parameterList = []) {
				$exception = null;
				try {
					$statement = $this->pdo->prepare($query);
					$statement->setFetchMode(PDO::FETCH_ASSOC);
					$statement->execute($parameterList);
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
				$sql = 'CREATE TABLE ' . ($this->delimite($table = $schema->getName())) . " (\n\t";
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
				foreach ($schema->getLinkList() as $link) {
					$sql .= ",\n\tFOREIGN KEY (" . $this->delimite($link->getSourceProperty()->getName()) . ') REFERENCES ' . $this->delimite($link->getTargetSchema()->getName()) . '(' . $this->delimite($link->getTargetProperty()->getName()) . ') ON DELETE RESTRICT ON UPDATE RESTRICT';
				}
				$this->native($sql . "\n)");
			}

			/**
			 * @param IInsertQuery $insertQuery
			 *
			 * @throws \Throwable
			 */
			protected function executeInsertQuery(IInsertQuery $insertQuery) {
				$nameList = [];
				$parameterList = [];
				foreach ($this->schemaManager->sanitize($schema = $insertQuery->getSchema(), $insertQuery->getSource()) as $k => $v) {
					$nameList[] = $this->delimite($k);
					$parameterList['p_' . sha1($k)] = $v;
				}
				$sql = "INSERT INTO\n\t" . $this->delimite($schema->getName()) . " (\n\t\t" . implode(",\n\t\t", $nameList) . "\n\t) VALUES (\n\t\t";
				$this->native($sql . ':' . implode(",\n\t\t:", array_keys($parameterList)) . "\n\t)", $parameterList);
			}

			/**
			 * @param ICreateRelationQuery $createRelationQuery
			 *
			 * @throws \Throwable
			 */
			protected function executeCreateRelationQuery(ICreateRelationQuery $createRelationQuery) {
				$this->executeInsertQuery(new InsertQuery(($relation = $createRelationQuery->getRelation())->getSchema(), [
					($sourceLink = $relation->getSourceLink())->getSourceProperty()->getName() => $createRelationQuery->getFrom()[$sourceLink->getTargetProperty()->getName()],
					($targetLink = $relation->getTargetLink())->getSourceProperty()->getName() => $createRelationQuery->getTo()[$targetLink->getTargetProperty()->getName()],
				]));
			}

			/**
			 * @param ISelectQuery $selectQuery
			 *
			 * @return \PDOStatement
			 * @throws \Throwable
			 */
			protected function executeSelectQuery(ISelectQuery $selectQuery): \PDOStatement {
				$fromList = [];
				$whereList = null;
				$parameterList = [];
				$selected = null;
				foreach ($selectQuery->getTableList() as $table) {
					$alias = $this->delimite($table->getAlias());
					if ($table->isSelected()) {
						$selected = $alias . '.*';
					}
					$fromList[$alias] = $this->delimite($table->getSchema()->getName()) . ' ' . $alias;
					if ($table->hasWhere()) {
						$whereList[] = ($query = $this->fragmentWhereGroup($table->where()))->getQuery();
						$parameterList = array_merge($parameterList, $query->getParameterList());
					}
				}
				$sql = "SELECT\n\t" . $selected . "\nFROM\n\t" . implode(",\n\t", $fromList) . "\n";
				if ($whereList) {
					$sql .= 'WHERE' . implode("AND\n", $whereList);
				}
				return $this->native($sql, $parameterList);
			}

			/**
			 * @param IUpdateQuery $updateQuery
			 *
			 * @throws \Throwable
			 */
			protected function executeUpdateQuery(IUpdateQuery $updateQuery) {
				$sql = "UPDATE\n\t";
				$sql .= $this->delimite(($schema = $updateQuery->getSchema())->getName()) . ' ' . $this->delimite($updateQuery->getTable()->getAlias()) . "\n";
				$sql .= "SET\n\t";
				$parameterList = [];
				$nameList = [];
				foreach ($this->schemaManager->sanitize($schema, $updateQuery->getSource()) as $k => $v) {
					$nameList[] = $this->delimite($k) . ' = :' . ($parameterId = ('p_' . sha1($k)));
					$parameterList[$parameterId] = $v;
				}
				$sql .= implode(",\n\t", $nameList) . "\n";
				if ($updateQuery->hasWhere()) {
					$sql .= 'WHERE' . ($query = $this->fragmentWhereGroup($updateQuery->where()))->getQuery();
					$parameterList = array_merge($parameterList, $query->getParameterList());
				}
				$this->native($sql, $parameterList);
			}

			/**
			 * @param IWhereTo $whereTo
			 *
			 * @return INativeQuery
			 * @throws DriverQueryException
			 * @throws \Exception
			 */
			protected function fragmentWhereTo(IWhereTo $whereTo): INativeQuery {
				$name = $this->delimite($whereTo->getTable()->getAlias()) . '.' . $this->delimite($whereTo->getName());
				switch ($target = $whereTo->getTarget()) {
					case 'column':
						list($prefix, $column) = $whereTo->getValue();
						return new NativeQuery($name . ' = ' . $this->delimite($prefix) . '.' . $this->delimite($column));
					case 'value':
						return new NativeQuery($name . ' = :' . ($parameterId = 'p_' . sha1($target . microtime(true) . random_bytes(8))), [
							$parameterId => $whereTo->getValue(),
						]);
				}
				throw new DriverQueryException(sprintf('Unknown where expression [%s] target [%s].', $whereTo->getType(), $target));
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
