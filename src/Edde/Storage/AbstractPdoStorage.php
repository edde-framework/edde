<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Collection\ICollection;
	use Edde\Config\ConfigException;
	use Edde\Entity\IEntity;
	use Edde\Query\INative;
	use Edde\Query\ISelectQuery;
	use Edde\Schema\ISchema;
	use Edde\Service\Schema\SchemaManager;
	use Iterator;
	use PDO;
	use PDOException;
	use stdClass;
	use Throwable;
	use function implode;

	abstract class AbstractPdoStorage extends AbstractStorage {
		use SchemaManager;
		/** @var array */
		protected $options;
		/** @var PDO */
		protected $pdo;

		/** @inheritdoc */
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
				if (empty($params) === false) {
					throw new StorageException(sprintf('%s does not support params.', __METHOD__));
				}
				return $this->pdo->exec($query);
			} catch (PDOException $exception) {
				throw $this->exception($exception);
			}
		}

		protected function executeSelect(ISelectQuery $selectQuery): INative {
		}

		/** @inheritdoc */
		public function create(string $name): IStorage {
			try {
				$schema = $this->schemaManager->getSchema($name);
				$sql = 'CREATE TABLE ' . $this->delimit($table = $schema->getRealName()) . " (\n\t";
				$columns = [];
				$primary = null;
				foreach ($schema->getAttributes() as $property) {
					$column = ($fragment = $this->delimit($property->getName())) . ' ' . $this->type($property->getType());
					if ($property->isPrimary()) {
						$primary = $fragment;
					} else if ($property->isUnique()) {
						$column .= ' UNIQUE';
					}
					if ($property->isRequired()) {
						$column .= ' NOT NULL';
					}
					$columns[] = $column;
				}
				if ($primary) {
					$columns[] = "CONSTRAINT " . $this->delimit(sha1($table . '.primary.' . $primary)) . ' PRIMARY KEY (' . $primary . ')';
				}
				$this->exec($sql . implode(",\n\t", $columns) . "\n)");
				return $this;
			} catch (Throwable $exception) {
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function insert(stdClass $source, string $name): stdClass {
			try {
				$this->schemaManager->validate(
					$schema = $this->schemaManager->getSchema($name),
					$generated = clone ($source = $this->schemaManager->generate(
						$schema,
						$source
					))
				);
				$table = $this->delimit($schema->getRealName());
				$source = $this->schemaManager->sanitize($schema, $source);
				$columns = [];
				$params = [];
				foreach ($source as $k => $v) {
					$columns[] = $delimited = $this->delimit($k);
					$params[$paramId = sha1($k)] = $v;
				}
				$sql = 'INSERT INTO ' . $table . ' (' . implode(', ', $columns) . ') VALUES (:' . implode(', :', array_keys($params)) . ')';
				$this->fetch($sql, $params);
				return $generated;
			} catch (Throwable $exception) {
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function update(stdClass $source, ISchema $schema): IStorage {
			return $this;
		}

		/** @inheritdoc */
		public function load(string $name, string $key): stdClass {
		}

		/** @inheritdoc */
		public function collection(ICollection $collection): Iterator {
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
			return $throwable;
		}

		protected function executeSelectQuery(SelectQuery $selectQuery) {
			$alias = $this->delimit($selectQuery->getAlias());
			$current = $selectQuery->getAlias();
			$params = [];
			$schema = $selectQuery->getSchema();
			$linkSql = '';
			foreach ($selectQuery->getJoins() as $name => $join) {
				if ($join->isLink()) {
					$link = $schema->getLink($join->getSchema());
					$linkSql .= ' INNER JOIN ' . $this->delimit($link->getTo()->getRealName()) . ' ' . $relation = $this->delimit($name);
					$from = ($this->delimit($current) . '.' . $this->delimit($link->getFrom()->getPropertyName()));
					$linkSql .= ' ON ' . $relation . '.' . $this->delimit($link->getTo()->getPropertyName()) . ' = ' . $from;
					$schema = $link->getTo()->getSchema();
					$current = $name;
					continue;
				}
				$relation = $schema->getRelation($join->getSchema(), $join->getRelation());
				$linkSql .= ' INNER JOIN ' . $this->delimit($relation->getSchema()->getRealName()) . ' ' . ($join = $this->delimit($current . '\\r'));
				$from = ($this->delimit($current) . '.' . $this->delimit($relation->getFrom()->getFrom()->getPropertyName()));
				$linkSql .= ' ON ' . $join . '.' . $this->delimit($relation->getFrom()->getTo()->getPropertyName()) . ' = ' . $from;
				$linkSql .= ' INNER JOIN ' . $this->delimit($relation->getTo()->getTo()->getRealName()) . ' ' . $this->delimit($name);
				$to = $this->delimit($name) . '.' . $this->delimit($relation->getTo()->getTo()->getPropertyName());
				$linkSql .= ' ON ' . $join . '.' . $this->delimit($relation->getTo()->getFrom()->getPropertyName()) . ' = ' . $to;
				$schema = $relation->getTo()->getTo()->getSchema();
				$current = $name;
			}
			$sql = 'SELECT ';
			$columns = [];
			foreach ($selectQuery->getSchemas() as $name => $sourceSchema) {
				if (empty($name)) {
					continue;
				}
				foreach ($sourceSchema->getAttributes() as $property) {
					$columns[] = $this->delimit($name) . '.' . $this->delimit($property->getName()) . ' AS ' . $this->delimit($name . '.' . $property->getName());
				}
			}
			$sql .= implode(', ', $columns);
			if ($selectQuery->isCount()) {
				$sql = 'SELECT COUNT(' . $selectQuery->getCount() . '.' . $this->delimit($schema->getPrimary()->getName()) . ') AS ' . $this->delimit($selectQuery->getCount() . '.count');
			}
			$sql .= ' FROM ' . $this->delimit($selectQuery->getSchema()->getRealName()) . ' ' . $alias;
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
						$name = $this->delimit(substr($column, 0, $dot)) . '.' . $this->delimit(substr($column, $dot + 1));
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

		protected function executeUnlinkQuery(UnlinkQuery $unlinkQuery) {
			$link = $unlinkQuery->getLink();
			$entity = $unlinkQuery->getEntity();
			$primary = $entity->getPrimary();
			$sql = 'UPDATE ' . $this->delimit($entity->getSchema()->getRealName()) . ' SET ' . $this->delimit($link->getFrom()->getPropertyName()) . ' = null WHERE ' . $this->delimit($primary->getName()) . ' = :a';
			return $this->fetch($sql, ['a' => $primary->get()]);
		}

		protected function executeLinkQuery(LinkQuery $linkQuery) {
			$link = $linkQuery->getLink();
			$entity = $linkQuery->getEntity();
			$primary = $entity->getPrimary();
			$sql = 'UPDATE ' . $this->delimit($entity->getSchema()->getRealName()) . ' SET ' . $this->delimit($link->getFrom()->getPropertyName()) . ' = :b WHERE ' . $this->delimit($primary->getName()) . ' = :a';
			$entity->set($link->getFrom()->getPropertyName(), $to = $linkQuery->getTo()->getPrimary()->get());
			return $this->fetch($sql, ['a' => $primary->get(), 'b' => $to]);
		}

		protected function executeRelationQuery(RelationQuery $relationQuery) {
			$using = $relationQuery->getUsing();
			$relation = $relationQuery->getRelation();
			$columns = [];
			$values = [];
			$params = [];
			$using->set($relation->getFrom()->getTo()->getPropertyName(), $relationQuery->getEntity()->get($relation->getFrom()->getFrom()->getPropertyName()));
			$using->set($relation->getTo()->getFrom()->getPropertyName(), $relationQuery->getTarget()->get($relation->getTo()->getTo()->getPropertyName()));
			foreach ($using->sanitize() as $k => $v) {
				$columns[] = $this->delimit($k);
				$params[$paramId = sha1($k)] = $v;
				$values[] = $paramId;
			}
			$sql = 'INSERT INTO ' . $this->delimit($relation->getSchema()->getRealName()) . ' (' . implode(', ', $columns) . ') VALUES (';
			$sql .= ':' . implode(', :', $values);
			$sql .= ')';
			return $this->fetch($sql, $params);
		}

		protected function executeDeleteQuery(DeleteQuery $deleteQuery) {
			$entity = $deleteQuery->getEntity();
			$primary = $entity->getPrimary();
			$sql = 'DELETE FROM ' . $this->delimit($entity->getSchema()->getRealName()) . ' WHERE ' . $this->delimit($primary->getName()) . ' = :a';
			return $this->fetch($sql, ['a' => $primary->get()]);
		}

		protected function executeDetachQuery(DetachQuery $detachQuery) {
			$relation = $detachQuery->getRelation();
			$sql = $this->getDeleteSql($relation->getSchema()->getRealName());
			$sql .= '(r.' . $this->delimit($relation->getFrom()->getTo()->getPropertyName()) . ' = :a AND ';
			$sql .= 'r.' . $this->delimit($relation->getTo()->getFrom()->getPropertyName()) . ' = :b) ';
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

		protected function executeDisconnectQuery(DisconnectQuery $disconnectQuery) {
			$relation = $disconnectQuery->getRelation();
			$sql = $this->getDeleteSql($relation->getSchema()->getRealName());
			$sql .= '(r.' . $this->delimit($relation->getFrom()->getTo()->getPropertyName()) . ' = :a)';
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
			return 'DELETE r FROM ' . $this->delimit($relation) . ' AS r WHERE ';
		}

		protected function fragmentWhere(IWhere $where) {
			[$expression, $type] = $params = $where->getWhere();
			switch ($expression) {
				case '=':
					$name = $this->delimit($params[2]);
					if (($dot = strpos($params[2], '.')) !== false) {
						$name = $this->delimit(substr($params[2], 0, $dot)) . '.' . $this->delimit(substr($params[2], $dot + 1));
					}
					switch ($type) {
						case 'expression':
							return new NativeQuery($name . ' ' . $expression . ' :' . ($parameterId = sha1($name . $expression)), [
								$parameterId => $params[3],
							]);
					}
					throw new StorageException(sprintf('Unknown where operator [%s] target [%s].', get_class($where), $type));
				case 'null':
					$name = $this->delimit($params[2]);
					if (($dot = strpos($params[2], '.')) !== false) {
						$name = $this->delimit(substr($params[2], 0, $dot)) . '.' . $this->delimit(substr($params[2], $dot + 1));
					}
					return new NativeQuery($name . ' IS NULL');
				case 'not-null':
					$name = $this->delimit($params[2]);
					if (($dot = strpos($params[2], '.')) !== false) {
						$name = $this->delimit(substr($params[2], 0, $dot)) . '.' . $this->delimit(substr($params[2], $dot + 1));
					}
					return new NativeQuery($name . ' IS NOT NULL');
				default:
					throw new StorageException(sprintf('Unknown where type [%s] for clause [%s].', $expression, get_class($where)));
			}
		}

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
				$table = $this->delimit($schema->getRealName());
				$primary = $entity->getPrimary();
				$count = ['count' => 0];
				foreach ($this->fetch('SELECT COUNT(' . ($delimitedPrimary = $this->delimit($primary->getName())) . ') AS count FROM ' . $table . ' WHERE ' . $delimitedPrimary . ' = :a LIMIT 1', ['a' => $primary->get()]) as $count) {
					break;
				}
				$source = $this->schemaManager->sanitize($schema, $entity->toArray());
				$columns = [];
				$params = [];
				$set = [];
				foreach ($source as $k => $v) {
					$columns[] = $delimited = $this->delimit($k);
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
			$this->pdo = new PDO(
				$this->section->require('dsn'),
				$this->section->require('user'),
				$this->section->optional('password'),
				$this->options
			);
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
			$this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$this->pdo->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_EMPTY_STRING);
			$this->pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
			$this->pdo->setAttribute(PDO::ATTR_TIMEOUT, 120);
		}

		abstract public function delimit(string $delimit): string;

		/**
		 * @param string $type
		 *
		 * @return string
		 *
		 * @throws StorageException
		 */
		abstract public function type(string $type): string;
	}
