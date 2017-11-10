<?php
	declare(strict_types=1);
	namespace Edde\Ext\Driver;

		use Edde\Api\Driver\Exception\DriverException;
		use Edde\Api\Driver\Exception\DriverQueryException;
		use Edde\Api\Driver\IDriver;
		use Edde\Api\Entity\Query\IDeleteQuery;
		use Edde\Api\Entity\Query\ILinkQuery;
		use Edde\Api\Entity\Query\IQueryQueue;
		use Edde\Api\Entity\Query\IRelationQuery;
		use Edde\Api\Entity\Query\IUnlinkQuery;
		use Edde\Api\Storage\Query\Fragment\IWhere;
		use Edde\Api\Storage\Query\ICrateSchemaQuery;
		use Edde\Api\Storage\Query\ISelectQuery;
		use Edde\Common\Driver\AbstractDriver;
		use Edde\Common\Storage\Query\NativeQuery;
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
				$sql = 'CREATE TABLE ' . $this->delimite($table = $schema->getRealName()) . " (\n\t";
				$columns = [];
				$primaries = [];
				foreach ($schema->getProperties() as $property) {
					$column = ($name = $this->delimite($property->getName())) . ' ' . $this->type($property->getType());
					if ($property->isPrimary()) {
						$primaries[] = $name;
					} else if ($property->isUnique()) {
						$column .= ' UNIQUE';
					}
					if ($property->isRequired()) {
						$column .= ' NOT NULL';
					}
					$columns[] = $column;
				}
				if (empty($primaries) === false) {
					$columns[] = "CONSTRAINT " . $this->delimite(sha1($table . '_primary_' . $primary = implode(', ', $primaries))) . ' PRIMARY KEY (' . $primary . ')';
				}
				$sql .= implode(",\n\t", $columns);
				foreach ($schema->getLinks() as $link) {
					$sql .= ",\n\tFOREIGN KEY (" . $this->delimite($link->getFrom()->getPropertyName()) . ') REFERENCES ' . $this->delimite($link->getTo()->getRealName()) . '(' . $this->delimite($link->getTo()->getPropertyName()) . ') ON DELETE CASCADE ON UPDATE CASCADE';
				}
				$this->native($sql . "\n)");
			}

			/**
			 * @param ISelectQuery $selectQuery
			 *
			 * @return \PDOStatement
			 * @throws DriverException
			 * @throws \Throwable
			 */
			protected function executeSelectQuery(ISelectQuery $selectQuery) {
				$return = $this->delimite($selectQuery->getReturn());
				$alias = $this->delimite($selectQuery->getAlias());
				$params = [];
				$schema = $selectQuery->getSchema();
				$current = $selectQuery->getAlias();
				$sql = '';
				foreach ($selectQuery->getJoins() as $name => $join) {
					if ($join->isLink()) {
						$link = $schema->getLink($join->getSchema());
						$sql .= ' INNER JOIN ' . $this->delimite($link->getTo()->getRealName()) . ' ' . $relation = $this->delimite($name);
						$from = ($this->delimite($current) . '.' . $this->delimite($link->getFrom()->getPropertyName()));
						$sql .= ' ON ' . $relation . '.' . $this->delimite($link->getTo()->getPropertyName()) . ' = ' . $from;
						$schema = $link->getTo()->getSchema();
						$return = $this->delimite($current = $name);
						continue;
					}
					$relation = $schema->getRelation($join->getSchema());
					$sql .= ' INNER JOIN ' . $this->delimite($relation->getSchema()->getRealName()) . ' ' . ($join = $this->delimite($current . '\\r'));
					$from = ($this->delimite($current) . '.' . $this->delimite($relation->getFrom()->getFrom()->getPropertyName()));
					$sql .= ' ON ' . $join . '.' . $this->delimite($relation->getFrom()->getTo()->getPropertyName()) . ' = ' . $from;
					$sql .= ' INNER JOIN ' . $this->delimite($relation->getTo()->getTo()->getRealName()) . ' ' . $this->delimite($name);
					$to = $this->delimite($name) . '.' . $this->delimite($relation->getTo()->getTo()->getPropertyName());
					$sql .= ' ON ' . $join . '.' . $this->delimite($relation->getTo()->getFrom()->getPropertyName()) . ' = ' . $to;
					$schema = $relation->getTo()->getTo()->getSchema();
					$return = $this->delimite($current = $name);
				}
				$sql = 'SELECT ' . $return . '.* FROM ' . $this->delimite($selectQuery->getSchema()->getRealName()) . ' ' . $alias . $sql;
				if ($selectQuery->hasWhere()) {
					$sql .= ' WHERE' . ($query = $this->fragmentWhereGroup($selectQuery->getWhere()))->getQuery();
					$params = $query->getParams();
				}
				if ($selectQuery->hasOrder()) {
					$orders = [];
					foreach ($selectQuery->getOrders() as $column => $asc) {
						$name = $alias;
						if (($dot = strpos($column, '.')) !== false) {
							$name = $this->delimite(substr($column, 0, $dot)) . '.' . $this->delimite(substr($column, $dot + 1));
						}
						$orders[] = $name . ' ' . ($asc ? 'ASC' : 'DESC');
					}
					$sql .= ' ORDER BY ' . implode(', ', $orders);
				}
				return $this->native($sql, $params);
			}

			/**
			 * @param IUnlinkQuery $unlinkQuery
			 *
			 * @throws \Throwable
			 */
			protected function executeUnlinkQuery(IUnlinkQuery $unlinkQuery) {
				$link = $unlinkQuery->getLink();
				$entity = $unlinkQuery->getEntity();
				$primary = $entity->getPrimary();
				$sql = 'UPDATE ' . $this->delimite($entity->getSchema()->getRealName()) . ' SET ' . $this->delimite($link->getFrom()->getPropertyName()) . ' = null WHERE ' . $this->delimite($primary->getName()) . ' = :a';
				$this->native($sql, ['a' => $primary->get()]);
			}

			/**
			 * @param ILinkQuery $linkQuery
			 *
			 * @throws \Throwable
			 */
			protected function executeLinkQuery(ILinkQuery $linkQuery) {
				$link = $linkQuery->getLink();
				$entity = $linkQuery->getEntity();
				$primary = $entity->getPrimary();
				$sql = 'UPDATE ' . $this->delimite($entity->getSchema()->getRealName()) . ' SET ' . $this->delimite($link->getFrom()->getPropertyName()) . ' = :b WHERE ' . $this->delimite($primary->getName()) . ' = :a';
				$this->native($sql, ['a' => $primary->get(), 'b' => $linkQuery->getTo()->getPrimary()->get()]);
			}

			/**
			 * @param IRelationQuery $relationQuery
			 *
			 * @throws \Throwable
			 */
			protected function executeRelationQuery(IRelationQuery $relationQuery) {
				$using = $relationQuery->getUsing();
				$relation = $relationQuery->getRelation();
				$columns = [];
				$values = [];
				$params = [];
				$using->set($relation->getFrom()->getTo()->getPropertyName(), $relationQuery->getEntity()->get($relation->getFrom()->getFrom()->getPropertyName()));
				$using->set($relation->getTo()->getFrom()->getPropertyName(), $relationQuery->getTarget()->get($relation->getTo()->getTo()->getPropertyName()));
				foreach ($using->sanitize() as $k => $v) {
					$columns[] = $this->delimite($k);
					$params[$paramId = sha1($k)] = $v;
					$values[] = $paramId;
				}
				$sql = 'INSERT INTO ' . $this->delimite($relation->getSchema()->getRealName()) . ' (' . implode(', ', $columns) . ') VALUES (';
				$sql .= ':' . implode(', :', $values);
				$sql .= ')';
				$this->native($sql, $params);
			}

			/**
			 * @param IDeleteQuery $deleteQuery
			 *
			 * @throws \Throwable
			 */
			protected function executeDeleteQuery(IDeleteQuery $deleteQuery) {
				$entity = $deleteQuery->getEntity();
				$primary = $entity->getPrimary();
				$sql = 'DELETE FROM ' . $this->delimite($entity->getSchema()->getRealName()) . ' WHERE ' . $this->delimite($primary->getName()) . ' = :a';
				$this->native($sql, ['a' => $primary->get()]);
			}

			/**
			 * @param IWhere $where
			 *
			 * @return NativeQuery
			 * @throws DriverQueryException
			 */
			protected function fragmentWhere(IWhere $where) {
				list($operator, $type) = $params = $where->getWhere();
				switch ($operator) {
					case '=':
						$name = $this->delimite($params[2]);
						if (($dot = strpos($params[2], '.')) !== false) {
							$name = $this->delimite(substr($params[2], 0, $dot)) . '.' . $this->delimite(substr($params[2], $dot + 1));
						}
						switch ($type) {
							case 'value':
								return new NativeQuery($name . ' ' . $operator . ' :' . ($parameterId = sha1($name . $operator)), [
									$parameterId => $params[3],
								]);
						}
						throw new DriverQueryException(sprintf('Unknown where operator [%s] target [%s].', get_class($where), $type));
					default:
						throw new DriverQueryException(sprintf('Unknown where type [%s] for clause [%s].', $operator, get_class($where)));
				}
			}

			/**
			 * @param IQueryQueue $queryQueue
			 *
			 * @throws DriverException
			 * @throws \Throwable
			 */
			protected function executeQueryQueue(IQueryQueue $queryQueue) {
				$entityQueue = $queryQueue->getEntityQueue();
				if ($entityQueue->isEmpty()) {
					return;
				}
				foreach ($entityQueue->getEntities() as $entity) {
					$schema = $entity->getSchema();
					if ($entity->isDirty() === false || $schema->isRelation()) {
						continue;
					}
					$table = $this->delimite($schema->getRealName());
					$primary = $entity->getPrimary();
					$count = ['count' => 0];
					foreach ($this->native('SELECT COUNT(' . ($delimitedPrimary = $this->delimite($primary->getName())) . ') AS count FROM ' . $table . ' WHERE ' . $delimitedPrimary . ' = :a LIMIT 1', ['a' => $primary->get()]) as $count) {
						break;
					}
					$source = $this->schemaManager->sanitize($schema, $entity->toArray());
					$columns = [];
					$params = [];
					$set = [];
					foreach ($source as $k => $v) {
						$columns[] = $delimited = $this->delimite($k);
						$params[$paramId = sha1($k)] = $v;
						$set[] = $delimited . ' = :' . $paramId;
					}
					$sql = 'INSERT INTO ' . $table . ' (' . implode(', ', $columns) . ') VALUES (:' . implode(', :', array_keys($params)) . ')';
					if ($count['count'] !== 0) {
						$sql = 'UPDATE ' . $table . ' SET ' . implode(', ', $set) . ' WHERE ' . $delimitedPrimary . ' = :a';
						$params['a'] = $primary->get();
					}
					$this->native($sql, $params);
				}
				foreach ($entityQueue->getQueries() as $query) {
					$this->execute($query);
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
