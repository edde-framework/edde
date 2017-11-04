<?php
	declare(strict_types=1);
	namespace Edde\Ext\Driver;

		use Edde\Api\Driver\Exception\DriverException;
		use Edde\Api\Driver\Exception\DriverQueryException;
		use Edde\Api\Driver\IDriver;
		use Edde\Api\Query\Fragment\IWhere;
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
				$this->executeInsertQuery(new InsertQuery(($relation = $createRelationQuery->getRelation())->getSchema(), array_merge($createRelationQuery->getSource(), [
					($sourceLink = $relation->getSourceLink())->getSourceProperty()->getName() => $createRelationQuery->getFrom()[$sourceLink->getTargetProperty()->getName()],
					($targetLink = $relation->getTargetLink())->getSourceProperty()->getName() => $createRelationQuery->getTo()[$targetLink->getTargetProperty()->getName()],
				])));
			}

			/**
			 * @param ISelectQuery $selectQuery
			 *
			 * @return \PDOStatement
			 * @throws \Throwable
			 */
			protected function executeSelectQuery(ISelectQuery $selectQuery): \PDOStatement {
				$select = null;
				$alias = $this->delimite($current = ($table = $selectQuery->getTable())->getAlias());
				$select = $this->delimite($table->getSelect()) . '.*';
				$schema = $table->getSchema();
				$from = $this->delimite($table->getSchema()->getName()) . ' ' . $alias;
				foreach ($table->getJoinList() as $name => $relation) {
					$relation = $schema->getRelation($relation);
					$sourceLink = $relation->getSourceLink();
					$targetLink = $relation->getTargetLink();
					$from .= "\n\tINNER JOIN " . $this->delimite($relation->getSchema()->getName()) . ' ' . ($join = $this->delimite($current . '\r')) . ' ON ';
					$from .= $current . '.' . $this->delimite($targetLink->getTargetProperty()->getName()) . ' = ' . $join . '.' . $this->delimite($sourceLink->getSourceProperty()->getName());
					$from .= "\n\tINNER JOIN " . $this->delimite($targetLink->getTargetSchema()->getName()) . ' ' . ($name = $this->delimite($current = $name)) . ' ON ';
					$from .= $join . '.' . $this->delimite($targetLink->getSourceProperty()->getName()) . ' = ' . $name . '.' . $this->delimite($sourceLink->getTargetProperty()->getName());
					$select = $name . '.*';
					$schema = $relation->getTargetLink()->getTargetSchema();
				}
				$sql = "SELECT\n\t" . $select . "\nFROM\n\t" . $from . "\n";
				$parameterList = [];
				if ($table->hasWhere()) {
					$sql .= 'WHERE' . ($query = $this->fragmentWhereGroup($table->where()))->getQuery();
					$parameterList = $query->getParameterList();
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
			 * @param IWhere $where
			 *
			 * @return INativeQuery
			 * @throws DriverQueryException
			 * @throws \Exception
			 */
			protected function fragmentWhere(IWhere $where): INativeQuery {
				list($operator, $type) = $parameters = $where->getWhere();
				switch ($operator) {
					case '=':
						$name = $this->delimite($parameters[2]);
						if (($dot = strpos($parameters[2], '.')) !== false) {
							$name = $this->delimite(substr($parameters[2], 0, $dot)) . '.' . $this->delimite(substr($parameters[2], $dot + 1));
						}
						switch ($type) {
							case 'value':
								return new NativeQuery($name . ' ' . $operator . ' :' . ($parameterId = 'p_' . sha1(random_bytes(42))), [
									$parameterId => $parameters[3],
								]);
						}
						throw new DriverQueryException(sprintf('Unknown where operator [%s] target [%s].', get_class($where), $type));
					default:
						throw new DriverQueryException(sprintf('Unknown where type [%s] for clause [%s].', $operator, get_class($where)));
				}
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
