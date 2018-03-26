<?php
	declare(strict_types=1);
	namespace Edde\Connection;

	use Edde\Config\ConfigException;
	use Edde\Entity\IEntity;
	use Edde\Query\DeleteQuery;
	use Edde\Query\DetachQuery;
	use Edde\Query\Fragment\IWhere;
	use Edde\Query\IDisconnectQuery;
	use Edde\Query\ISelectQuery;
	use Edde\Query\LinkQuery;
	use Edde\Query\NativeQuery;
	use Edde\Query\QueryQueue;
	use Edde\Query\RelationQuery;
	use Edde\Query\UnlinkQuery;
	use Edde\Schema\SchemaException;
	use Edde\Service\Schema\SchemaManager;
	use PDO;
	use PDOException;
	use PDOStatement;
	use Throwable;
	use function implode;

	abstract class AbstractPdoConnection extends AbstractConnection {
		use SchemaManager;
		protected $options;
		/** @var PDO */
		protected $pdo;

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
				return $this->pdo->exec($query);
			} catch (PDOException $exception) {
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function create(string $name): IConnection {
			try {
				$schema = $this->schemaManager->load($name);
				$sql = 'CREATE TABLE ' . $this->delimite($table = $schema->getRealName()) . " (\n\t";
				$columns = [];
				$primaries = [];
				foreach ($schema->getProperties() as $property) {
					$column = ($fragment = $this->delimite($property->getName())) . ' ' . $this->type($property->getType());
					if ($property->isPrimary()) {
						$primaries[] = $fragment;
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
				$this->exec($sql . "\n)");
				return $this;
			} catch (Throwable $exception) {
				throw new ConnectionException(sprintf('Cannot create schema [%s]: %s', $name, $exception->getMessage()), 0, $exception);
			}
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
			return new ConnectionException('Unhandled exception: ' . $throwable->getMessage(), 0, $throwable);
		}

		/**
		 * @param ISelectQuery $selectQuery
		 *
		 * @return PDOStatement
		 * @throws ConnectionException
		 * @throws Throwable
		 */
		protected function executeSelectQuery(ISelectQuery $selectQuery) {
			$alias = $this->delimite($selectQuery->getAlias());
			$current = $selectQuery->getAlias();
			$params = [];
			$schema = $selectQuery->getSchema();
			$linkSql = '';
			foreach ($selectQuery->getJoins() as $name => $join) {
				if ($join->isLink()) {
					$link = $schema->getLink($join->getSchema());
					$linkSql .= ' INNER JOIN ' . $this->delimite($link->getTo()->getRealName()) . ' ' . $relation = $this->delimite($name);
					$from = ($this->delimite($current) . '.' . $this->delimite($link->getFrom()->getPropertyName()));
					$linkSql .= ' ON ' . $relation . '.' . $this->delimite($link->getTo()->getPropertyName()) . ' = ' . $from;
					$schema = $link->getTo()->getSchema();
					$current = $name;
					continue;
				}
				$relation = $schema->getRelation($join->getSchema(), $join->getRelation());
				$linkSql .= ' INNER JOIN ' . $this->delimite($relation->getSchema()->getRealName()) . ' ' . ($join = $this->delimite($current . '\\r'));
				$from = ($this->delimite($current) . '.' . $this->delimite($relation->getFrom()->getFrom()->getPropertyName()));
				$linkSql .= ' ON ' . $join . '.' . $this->delimite($relation->getFrom()->getTo()->getPropertyName()) . ' = ' . $from;
				$linkSql .= ' INNER JOIN ' . $this->delimite($relation->getTo()->getTo()->getRealName()) . ' ' . $this->delimite($name);
				$to = $this->delimite($name) . '.' . $this->delimite($relation->getTo()->getTo()->getPropertyName());
				$linkSql .= ' ON ' . $join . '.' . $this->delimite($relation->getTo()->getFrom()->getPropertyName()) . ' = ' . $to;
				$schema = $relation->getTo()->getTo()->getSchema();
				$current = $name;
			}
			$sql = 'SELECT ';
			$columns = [];
			foreach ($selectQuery->getSchemas() as $name => $sourceSchema) {
				if (empty($name)) {
					continue;
				}
				foreach ($sourceSchema->getProperties() as $property) {
					$columns[] = $this->delimite($name) . '.' . $this->delimite($property->getName()) . ' AS ' . $this->delimite($name . '.' . $property->getName());
				}
			}
			$sql .= implode(', ', $columns);
			if ($selectQuery->isCount()) {
				$sql = 'SELECT COUNT(' . $selectQuery->getCount() . '.' . $this->delimite($schema->getPrimary()->getName()) . ') AS ' . $this->delimite($selectQuery->getCount() . '.count');
			}
			$sql .= ' FROM ' . $this->delimite($selectQuery->getSchema()->getRealName()) . ' ' . $alias;
			$sql .= $linkSql;
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
			if ($selectQuery->hasLimit()) {
				[$limit, $offset] = $selectQuery->getLimit();
				$sql .= ' LIMIT ' . $limit . ' OFFSET ' . ($limit * $offset);
			}
			return $this->fetch($sql, $params);
		}

		/**
		 * @param UnlinkQuery $unlinkQuery
		 *
		 * @return mixed|PDOStatement
		 *
		 * @throws Throwable
		 */
		protected function executeUnlinkQuery(UnlinkQuery $unlinkQuery) {
			$link = $unlinkQuery->getLink();
			$entity = $unlinkQuery->getEntity();
			$primary = $entity->getPrimary();
			$sql = 'UPDATE ' . $this->delimite($entity->getSchema()->getRealName()) . ' SET ' . $this->delimite($link->getFrom()->getPropertyName()) . ' = null WHERE ' . $this->delimite($primary->getName()) . ' = :a';
			return $this->fetch($sql, ['a' => $primary->get()]);
		}

		/**
		 * @param LinkQuery $linkQuery
		 *
		 * @return mixed|PDOStatement
		 *
		 * @throws ConnectionException
		 * @throws SchemaException
		 * @throws Throwable
		 */
		protected function executeLinkQuery(LinkQuery $linkQuery) {
			$link = $linkQuery->getLink();
			$entity = $linkQuery->getEntity();
			$primary = $entity->getPrimary();
			$sql = 'UPDATE ' . $this->delimite($entity->getSchema()->getRealName()) . ' SET ' . $this->delimite($link->getFrom()->getPropertyName()) . ' = :b WHERE ' . $this->delimite($primary->getName()) . ' = :a';
			$entity->set($link->getFrom()->getPropertyName(), $to = $linkQuery->getTo()->getPrimary()->get());
			return $this->fetch($sql, ['a' => $primary->get(), 'b' => $to]);
		}

		/**
		 * @param RelationQuery $relationQuery
		 *
		 * @return mixed|PDOStatement
		 *
		 * @throws ConnectionException
		 * @throws Throwable
		 */
		protected function executeRelationQuery(RelationQuery $relationQuery) {
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
			return $this->fetch($sql, $params);
		}

		/**
		 * @param DeleteQuery $deleteQuery
		 *
		 * @return mixed|PDOStatement
		 *
		 * @throws Throwable
		 */
		protected function executeDeleteQuery(DeleteQuery $deleteQuery) {
			$entity = $deleteQuery->getEntity();
			$primary = $entity->getPrimary();
			$sql = 'DELETE FROM ' . $this->delimite($entity->getSchema()->getRealName()) . ' WHERE ' . $this->delimite($primary->getName()) . ' = :a';
			return $this->fetch($sql, ['a' => $primary->get()]);
		}

		/**
		 * @param DetachQuery $detachQuery
		 *
		 * @return mixed|PDOStatement
		 *
		 * @throws ConnectionException
		 * @throws Throwable
		 * @throws SchemaException
		 */
		protected function executeDetachQuery(DetachQuery $detachQuery) {
			$relation = $detachQuery->getRelation();
			$sql = $this->getDeleteSql($relation->getSchema()->getRealName());
			$sql .= '(r.' . $this->delimite($relation->getFrom()->getTo()->getPropertyName()) . ' = :a AND ';
			$sql .= 'r.' . $this->delimite($relation->getTo()->getFrom()->getPropertyName()) . ' = :b) ';
			/** @var $entity IEntity[] */
			$entity = [
				$detachQuery->getEntity(),
				$detachQuery->getTarget(),
			];
			$params = [];
			if ($detachQuery->hasWhere()) {
				$sql .= ' AND (' . ($query = $this->fragmentWhereGroup($detachQuery->getWhere()))->getQuery() . ')';
				$params = $query->getParams();
			}
			$params['a'] = $entity[0]->getPrimary()->get();
			$params['b'] = $entity[1]->getPrimary()->get();
			return $this->fetch($sql, $params);
		}

		/**
		 * @param IDisconnectQuery $disconnectQuery
		 *
		 * @return mixed|PDOStatement
		 *
		 * @throws ConnectionException
		 * @throws Throwable
		 */
		protected function executeDisconnectQuery(IDisconnectQuery $disconnectQuery) {
			$relation = $disconnectQuery->getRelation();
			$sql = $this->getDeleteSql($relation->getSchema()->getRealName());
			$sql .= '(r.' . $this->delimite($relation->getFrom()->getTo()->getPropertyName()) . ' = :a)';
			$entity = $disconnectQuery->getEntity();
			$primary = $entity->getPrimary();
			$params = [];
			if ($disconnectQuery->hasWhere()) {
				$sql .= ' AND (' . ($query = $this->fragmentWhereGroup($disconnectQuery->getWhere()))->getQuery() . ')';
				$params = $query->getParams();
			}
			$params['a'] = $primary->get();
			return $this->fetch($sql, $params);
		}

		protected function getDeleteSql(string $relation): string {
			return 'DELETE r FROM ' . $this->delimite($relation) . ' AS r WHERE ';
		}

		/**
		 * @param \Edde\Query\Fragment\IWhere $where
		 *
		 * @return NativeQuery
		 *
		 * @throws ConnectionException
		 */
		protected function fragmentWhere(IWhere $where) {
			[$expression, $type] = $params = $where->getWhere();
			switch ($expression) {
				case '=':
					$name = $this->delimite($params[2]);
					if (($dot = strpos($params[2], '.')) !== false) {
						$name = $this->delimite(substr($params[2], 0, $dot)) . '.' . $this->delimite(substr($params[2], $dot + 1));
					}
					switch ($type) {
						case 'expression':
							return new NativeQuery($name . ' ' . $expression . ' :' . ($parameterId = sha1($name . $expression)), [
								$parameterId => $params[3],
							]);
					}
					throw new ConnectionException(sprintf('Unknown where operator [%s] target [%s].', get_class($where), $type));
				case 'null':
					$name = $this->delimite($params[2]);
					if (($dot = strpos($params[2], '.')) !== false) {
						$name = $this->delimite(substr($params[2], 0, $dot)) . '.' . $this->delimite(substr($params[2], $dot + 1));
					}
					return new NativeQuery($name . ' IS NULL');
				case 'not-null':
					$name = $this->delimite($params[2]);
					if (($dot = strpos($params[2], '.')) !== false) {
						$name = $this->delimite(substr($params[2], 0, $dot)) . '.' . $this->delimite(substr($params[2], $dot + 1));
					}
					return new NativeQuery($name . ' IS NOT NULL');
				default:
					throw new ConnectionException(sprintf('Unknown where type [%s] for clause [%s].', $expression, get_class($where)));
			}
		}

		/**
		 * @param QueryQueue $queryQueue
		 *
		 * @throws ConnectionException
		 * @throws SchemaException
		 * @throws Throwable
		 */
		protected function executeQueryQueue(QueryQueue $queryQueue) {
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
				foreach ($this->fetch('SELECT COUNT(' . ($delimitedPrimary = $this->delimite($primary->getName())) . ') AS count FROM ' . $table . ' WHERE ' . $delimitedPrimary . ' = :a LIMIT 1', ['a' => $primary->get()]) as $count) {
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
					/**
					 * 'a'? Look at the line above
					 */
					$params['a'] = $primary->get();
				}
				$this->fetch($sql, $params);
			}
			foreach ($entityQueue->getQueries() as $query) {
				$this->execute($query);
			}
		}

		/**
		 * @inheritdoc
		 *
		 * @throws ConfigException
		 */
		public function handleSetup(): void {
			parent::handleSetup();
			$section = $this->configService->require($this->config);
			$this->pdo = new PDO(
				$section->require('dsn'),
				$section->require('user'),
				$section->optional('password'),
				$this->options
			);
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
			$this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$this->pdo->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_EMPTY_STRING);
			$this->pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
			$this->pdo->setAttribute(PDO::ATTR_TIMEOUT, 120);
		}

		abstract public function delimite(string $delimite): string;

		/**
		 * @param string $type
		 *
		 * @return string
		 *
		 * @throws ConnectionException
		 */
		abstract public function type(string $type): string;
	}
