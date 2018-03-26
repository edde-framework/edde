<?php
	declare(strict_types=1);
	namespace Edde\Connection;

	use Edde\Config\ConfigException;
	use Edde\Crate\IProperty;
	use Edde\Entity\IEntity;
	use Edde\Query\CreateSchemaQuery;
	use Edde\Query\DeleteQuery;
	use Edde\Query\DetachQuery;
	use Edde\Query\DisconnectQuery;
	use Edde\Query\Fragment\IWhere;
	use Edde\Query\INativeQuery;
	use Edde\Query\ISelectQuery;
	use Edde\Query\LinkQuery;
	use Edde\Query\NativeQuery;
	use Edde\Query\QueryQueue;
	use Edde\Query\RelationQuery;
	use Edde\Query\UnlinkQuery;
	use Edde\Schema\SchemaException;
	use Exception;
	use GraphAware\Bolt\Configuration;
	use GraphAware\Bolt\Exception\MessageFailureException;
	use GraphAware\Bolt\GraphDatabase;
	use GraphAware\Bolt\Protocol\SessionInterface;
	use GraphAware\Bolt\Protocol\V1\Transaction;
	use GraphAware\Bolt\Result\Result;
	use GraphAware\Common\Type\MapAccessor;
	use ReflectionException;
	use Throwable;
	use function array_merge;
	use function extract;
	use function implode;

	class Neo4jConnection extends AbstractConnection {
		/** @var SessionInterface */
		protected $session;
		/** @var Transaction */
		protected $transaction;

		public function __construct(string $config = 'neo4j') {
			parent::__construct($config);
		}

		/** @inheritdoc */
		public function fetch($query, array $params = []) {
			try {
				return (function (Result $result) {
					foreach ($result->getRecords() as $record) {
						$keys = $record->keys();
						$item = [];
						/** @var $value MapAccessor */
						foreach ($record->values() as $index => $value) {
							if ($value instanceof MapAccessor) {
								foreach ($value->asArray() as $k => $v) {
									$item[$keys[$index] . '.' . $k] = $v;
								}
								continue;
							}
							yield [$keys[$index] => $value];
						}
						yield $item;
					}
				})($this->session->run($query, $params));
			} catch (Throwable $throwable) {
				/** @noinspection PhpUnhandledExceptionInspection */
				throw $this->exception($throwable);
			}
		}

		/** @inheritdoc */
		public function exec($query, array $params = []) {
			return $this->fetch($query, $params);
		}

		/** @inheritdoc */
		public function onStart(): void {
			($this->transaction = $this->session->transaction())->begin();
		}

		/** @inheritdoc */
		public function onCommit(): void {
			try {
				$this->transaction->commit();
				$this->transaction = null;
			} catch (Throwable $throwable) {
				$this->exception($throwable);
			}
		}

		/** @inheritdoc */
		public function onRollback(): void {
			try {
				$this->transaction->rollback();
			} catch (MessageFailureException $exception) {
				/**
				 * this is incredibly ugly, but transaction state should be tracked in this driver, so it's
				 * possible to suppress this transaction; related to Neo4j, it's dying on transaction commit, thus
				 * making whole this stuff a bit more complicated
				 */
				if ($exception->getMessage() !== 'No current transaction to rollback.') {
					throw $exception;
				}
			}
			$this->transaction = null;
		}

		protected function exception(Throwable $throwable): Throwable {
			if (stripos($message = $throwable->getMessage(), 'already exists with label') !== false) {
				return new DuplicateEntryException($message, 0, $throwable);
			} else if (stripos($message, 'must have the property') !== false) {
				return new RequiredValueException($message, 0, $throwable);
			}
			return new ConnectionException($message, 0, $throwable);
		}

		protected function executeCreateSchemaQuery(CreateSchemaQuery $createSchemaQuery) {
			if (($schema = $createSchemaQuery->getSchema())->isRelation()) {
				return;
			}
			$primaries = null;
			$indexes = null;
			$delimited = $this->delimite($schema->getRealName());
			foreach ($schema->getProperties() as $property) {
				$name = $property->getName();
				$fragment = 'n.' . $this->delimite($name);
				if ($property->isPrimary()) {
					$primaries[] = $fragment;
				} else if ($property->isUnique()) {
					$this->fetch('CREATE CONSTRAINT ON (n:' . $delimited . ') ASSERT ' . $fragment . ' IS UNIQUE');
				}
				if ($property->isRequired()) {
					$this->fetch('CREATE CONSTRAINT ON (n:' . $delimited . ') ASSERT exists(' . $fragment . ')');
				}
			}
			if ($indexes) {
				$this->fetch('CREATE INDEX ON :' . $delimited . '(' . implode(',', $indexes) . ')');
			}
			if ($primaries) {
				$this->fetch('CREATE CONSTRAINT ON (n:' . $delimited . ') ASSERT (' . implode(', ', $primaries) . ') IS NODE KEY');
			}
		}

		/**
		 * @param QueryQueue $queryQueue
		 *
		 * @throws Exception
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
				$primary = $entity->getPrimary();
				$cypher = 'MERGE (a:' . $this->delimite($schema->getRealName()) . ' {' . $this->delimite($primary->getName()) . ': $primary})';
				$cypher .= " SET a = \$set\n";
				$this->fetch($cypher, [
					'primary' => $primary->get(),
					'set'     => $this->schemaManager->sanitize($schema, $entity->toArray()),
				]);
			}
			foreach ($entityQueue->getQueries() as $query) {
				$this->execute($query);
			}
		}

		/**
		 * @param DeleteQuery $deleteQuery
		 *
		 * @throws Throwable
		 */
		protected function executeDeleteQuery(DeleteQuery $deleteQuery) {
			$entity = $deleteQuery->getEntity();
			$primary = $entity->getPrimary();
			$cypher = 'MATCH (n:' . $this->delimite($entity->getSchema()->getRealName()) . ' {' . $this->delimite($primary->getName()) . ': $a}) DETACH DELETE n';
			$this->fetch($cypher, ['a' => $primary->get()]);
		}

		/**
		 * @param DetachQuery $detachQuery
		 *
		 * @throws ConnectionException
		 * @throws SchemaException
		 */
		protected function executeDetachQuery(DetachQuery $detachQuery) {
			/** @var $entity IEntity[] */
			$entity = [
				$detachQuery->getEntity(),
				$detachQuery->getTarget(),
			];
			/** @var $entity IProperty[] */
			$primary = [
				$entity[0]->getPrimary(),
				$entity[1]->getPrimary(),
			];
			$params = [];
			$cypher = 'MATCH (:' . $this->delimite($entity[0]->getSchema()->getRealName()) . " {" . $this->delimite($primary[0]->getName()) . ': $a})';
			$cypher .= '-[r:' . $this->delimite($detachQuery->getRelation()->getSchema()->getRealName()) . ']';
			$cypher .= '->(:' . $this->delimite($entity[1]->getSchema()->getRealName()) . " {" . $this->delimite($primary[1]->getName()) . ': $b})';
			if ($detachQuery->hasWhere()) {
				$cypher .= ' WHERE' . ($query = $this->fragmentWhereGroup($detachQuery->getWhere()))->getQuery();
				$params = $query->getParams();
			}
			$params['a'] = $primary[0]->get();
			$params['b'] = $primary[1]->get();
			$cypher .= ' DELETE r';
			$this->fetch($cypher, $params);
		}

		/**
		 * @param DisconnectQuery $disconnectQuery
		 *
		 * @throws ConnectionException
		 * @throws SchemaException
		 */
		protected function executeDisconnectQuery(DisconnectQuery $disconnectQuery) {
			$entity = $disconnectQuery->getEntity();
			$relation = $disconnectQuery->getRelation();
			$primary = $entity->getPrimary();
			$cypher = 'MATCH (:' . $this->delimite($entity->getSchema()->getRealName()) . " {" . $this->delimite($primary->getName()) . ': $a})';
			$cypher .= '-[r:' . $this->delimite($relation->getSchema()->getRealName()) . ']';
			$cypher .= '->(:' . $this->delimite($relation->getTo()->getTo()->getRealName()) . ')';
			$params = [];
			if ($disconnectQuery->hasWhere()) {
				$cypher .= ' WHERE' . ($query = $this->fragmentWhereGroup($disconnectQuery->getWhere()))->getQuery();
				$params = $query->getParams();
			}
			$params['a'] = $primary->get();
			$cypher .= ' DELETE r';
			$this->fetch($cypher, $params);
		}

		/**
		 * @param LinkQuery $linkQuery
		 *
		 * @throws Throwable
		 */
		protected function executeLinkQuery(LinkQuery $linkQuery) {
			$link = $linkQuery->getLink();
			$entity = $linkQuery->getEntity();
			$entity->set($link->getFrom()->getPropertyName(), $linkQuery->getTo()->getPrimary()->get());
			$this->link(
				$entity,
				$linkQuery->getTo(),
				$link->getName(),
				empty($entity->toArray()) === false ? $entity->sanitize() : null
			);
		}

		/**
		 * @param UnlinkQuery $unlinkQuery
		 *
		 * @throws Throwable
		 */
		protected function executeUnlinkQuery(UnlinkQuery $unlinkQuery) {
			$link = $unlinkQuery->getLink();
			$primary = $unlinkQuery->getEntity()->getPrimary();
			$cypher = 'MATCH (:' . $this->delimite($link->getFrom()->getRealName()) . " {" . $this->delimite($primary->getName()) . ': $a})-';
			$cypher .= '[r:' . ($unlinkQuery->getLink()->getName()) . ']';
			$cypher .= '->(:' . $this->delimite($link->getTo()->getRealName()) . ')';
			$cypher .= ' DELETE r';
			$this->fetch($cypher, ['a' => $primary->get()]);
		}

		/**
		 * @param RelationQuery $relationQuery
		 *
		 * @throws Throwable
		 */
		protected function executeRelationQuery(RelationQuery $relationQuery) {
			$using = $relationQuery->getUsing();
			$relation = $relationQuery->getRelation();
			$using->set($relation->getFrom()->getTo()->getPropertyName(), $relationQuery->getEntity()->get($relation->getFrom()->getFrom()->getPropertyName()));
			$using->set($relation->getTo()->getFrom()->getPropertyName(), $relationQuery->getTarget()->get($relation->getTo()->getTo()->getPropertyName()));
			$this->link(
				$relationQuery->getEntity(),
				$relationQuery->getTarget(),
				$relationQuery->getRelation()->getSchema()->getRealName(),
				empty($using->toArray()) === false ? $using->sanitize() : null
			);
		}

		/**
		 * @param IEntity    $from
		 * @param IEntity    $to
		 * @param string     $relation
		 * @param array|null $attributes
		 *
		 * @throws Throwable
		 */
		protected function link(IEntity $from, IEntity $to, string $relation, array $attributes = null) {
			$cypher = null;
			/** @var $entity IEntity[] */
			$entity = [
				$from,
				$to,
			];
			/** @var $schema string[] */
			$schema = [
				$entity[0]->getSchema()->getRealName(),
				$entity[1]->getSchema()->getRealName(),
			];
			/** @var $primary IProperty[] */
			$primary = [
				$entity[0]->getPrimary(),
				$entity[1]->getPrimary(),
			];
			$params = [
				'a' => $primary[0]->get(),
				'b' => $primary[1]->get(),
			];
			$cypher .= 'MERGE (a:' . $this->delimite($schema[0]) . ' {' . $this->delimite($primary[0]->getName()) . ": \$a})\n";
			$cypher .= 'MERGE (b:' . $this->delimite($schema[1]) . ' {' . $this->delimite($primary[1]->getName()) . ": \$b})\n";
			$cypher .= 'MERGE (a)';
			$cypher .= '-[:' . $this->delimite($relation);
			if (empty($attributes) === false) {
				$nativeQuery = $this->formatAttributes($attributes);
				$cypher .= $nativeQuery->getQuery();
				$params = array_merge($params, $nativeQuery->getParams());
			}
			$cypher .= ']';
			$cypher .= "->(b)\n";
			$this->fetch($cypher, $params);
		}

		protected function formatAttributes(array $attributes): INativeQuery {
			$propertyList = [];
			foreach ($attributes as $k => $v) {
				if ($v !== null) {
					$propertyList[] = $this->delimite($k) . ': $' . $this->delimite($parameterId = (sha1($k)));
					$params[$parameterId] = $v;
				}
			}
			return new NativeQuery('{' . implode(', ', $propertyList) . '}', $params);
		}

		/**
		 * @param ISelectQuery $selectQuery
		 *
		 * @return mixed
		 *
		 * @throws Throwable
		 */
		protected function executeSelectQuery(ISelectQuery $selectQuery) {
			$cypher = 'MATCH ';
			$params = [];
			$schema = $selectQuery->getSchema();
			$current = $selectQuery->getAlias();
			$cypher .= '(' . ($alias = $this->delimite($current)) . ':' . $this->delimite($schema->getRealName()) . ')';
			foreach ($selectQuery->getJoins() as $name => $join) {
				if ($join->isLink()) {
					$link = $schema->getLink($join->getSchema());
					$cypher .= '-[' . $this->delimite($current . '\r') . ':' . $this->delimite($link->getName()) . ']';
					$schema = $link->getTo()->getSchema();
					$cypher .= '->(' . $this->delimite($current = $name) . ':' . $this->delimite($schema->getRealName()) . ')';
					continue;
				}
				$relation = $schema->getRelation($join->getSchema(), $join->getRelation());
				$cypher .= '-[' . $this->delimite($current . '\r') . ':' . $this->delimite($relation->getSchema()->getRealName()) . ']';
				$schema = $relation->getTo()->getTo()->getSchema();
				$cypher .= '->(' . $this->delimite($current = $name) . ':' . $this->delimite($schema->getRealName()) . ')';
			}
			if ($selectQuery->hasWhere()) {
				$cypher .= ' WHERE' . ($query = $this->fragmentWhereGroup($selectQuery->getWhere()))->getQuery();
				$params = $query->getParams();
			}
			if ($selectQuery->isCount()) {
				$cypher .= ' RETURN COUNT(' . $this->delimite($selectQuery->getCount()) . ') AS ' . $this->delimite($selectQuery->getCount() . '.count');
			} else {
				$returns = [];
				foreach ($selectQuery->getSchemas() as $return => $_) {
					if (empty($return) === false) {
						$returns[] = $this->delimite($return);
					}
				}
				$cypher .= ' RETURN ' . implode(',', $returns);
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
				$cypher .= 'ORDER BY ' . implode(', ', $orders);
			}
			if ($selectQuery->hasLimit()) {
				[$limit, $offset] = $selectQuery->getLimit();
				$cypher .= ' SKIP ' . ($limit * $offset) . ' LIMIT ' . $limit;
			}
			return $this->fetch($cypher, $params);
		}

		/**
		 * @param \Edde\Query\Fragment\IWhere $where
		 *
		 * @return INativeQuery
		 *
		 * @throws ConnectionException
		 */
		protected function fragmentWhere(IWhere $where): INativeQuery {
			[$expression, $type] = $params = $where->getWhere();
			switch ($expression) {
				case '=':
					$name = $this->delimite($params[2]);
					if (($dot = strpos($params[2], '.')) !== false) {
						$name = $this->delimite(substr($params[2], 0, $dot)) . '.' . $this->delimite(substr($params[2], $dot + 1));
					}
					switch ($type) {
						case 'expression':
							return new NativeQuery($name . ' ' . $expression . ' $' . $this->delimite($parameterId = sha1($name . $expression)), [
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
				case 'related':
					extract($params[3]);
					/** @var $alias string */
					/** @var $relation string */
					/** @var $target string */
					$fragment = ' (' . $this->delimite($alias) . ')-[:' . $this->delimite($relation) . ']-';
					$fragment .= '(:' . $this->delimite($target);
					if (empty($params) === false) {
						$nativeQuery = $this->formatAttributes($params);
						$fragment .= ' ' . $nativeQuery->getQuery();
					}
					$fragment .= ')';
					return new NativeQuery($fragment, isset($nativeQuery) ? $nativeQuery->getParams() : []);
				case 'not-related':
					extract($params[3]);
					/** @var $alias string */
					/** @var $relation string */
					/** @var $target string */
					$fragment = 'NOT (' . $this->delimite($alias) . ')-[:' . $this->delimite($relation) . ']-';
					$fragment .= '(:' . $this->delimite($target);
					if (empty($params) === false) {
						$nativeQuery = $this->formatAttributes($params);
						$fragment .= ' ' . $nativeQuery->getQuery();
					}
					$fragment .= ')';
					return new NativeQuery($fragment, isset($nativeQuery) ? $nativeQuery->getParams() : []);
				default:
					throw new ConnectionException(sprintf('Unknown where type [%s] for clause [%s].', $expression, get_class($where)));
			}
		}

		/** @inheritdoc */
		public function delimite(string $delimite): string {
			return '`' . str_replace('`', '``', $delimite) . '`';
		}

		/**
		 * @inheritdoc
		 *
		 * @throws ReflectionException
		 * @throws ConfigException
		 */
		protected function handleSetup(): void {
			parent::handleSetup();
			$config = null;
			$section = $this->configService->require($this->config);
			if ($user = $section->optional('user')) {
				$config = Configuration::create()->withCredentials($user, $section->require('password'));
			}
			$this->session = GraphDatabase::driver($section->require('url'), $config)->session();
		}
	}
