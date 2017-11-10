<?php
	declare(strict_types=1);
	namespace Edde\Ext\Driver;

		use Edde\Api\Crate\IProperty;
		use Edde\Api\Driver\Exception\DriverException;
		use Edde\Api\Driver\Exception\DriverQueryException;
		use Edde\Api\Driver\IDriver;
		use Edde\Api\Entity\IEntity;
		use Edde\Api\Entity\Query\IDeleteQuery;
		use Edde\Api\Entity\Query\IDetachQuery;
		use Edde\Api\Entity\Query\ILinkQuery;
		use Edde\Api\Entity\Query\IQueryQueue;
		use Edde\Api\Entity\Query\IRelationQuery;
		use Edde\Api\Entity\Query\IUnlinkQuery;
		use Edde\Api\Storage\Exception\DuplicateEntryException;
		use Edde\Api\Storage\Exception\NullValueException;
		use Edde\Api\Storage\INativeQuery;
		use Edde\Api\Storage\Query\Fragment\IWhere;
		use Edde\Api\Storage\Query\ICrateSchemaQuery;
		use Edde\Api\Storage\Query\ISelectQuery;
		use Edde\Common\Driver\AbstractDriver;
		use Edde\Common\Storage\Query\NativeQuery;
		use GraphAware\Bolt\Exception\MessageFailureException;
		use GraphAware\Bolt\GraphDatabase;
		use GraphAware\Bolt\Protocol\SessionInterface;
		use GraphAware\Bolt\Protocol\V1\Transaction;
		use GraphAware\Bolt\Result\Result;
		use GraphAware\Common\Type\Node;

		class Neo4jDriver extends AbstractDriver {
			/** @var string */
			protected $url;
			/**
			 * @var SessionInterface
			 */
			protected $session;
			/**
			 * @var Transaction
			 */
			protected $transaction;

			/**
			 * @param string $url
			 */
			public function __construct(string $url) {
				$this->url = $url;
			}

			/**
			 * @inheritdoc
			 */
			public function native($query, array $params = []) {
				try {
					return (function (Result $result) {
						foreach ($result->getRecords() as $record) {
							/** @var $value Node */
							foreach ($record->values() as $value) {
								yield $value->asArray();
							}
						}
					})($this->session->run($query, $params));
				} catch (\Throwable $throwable) {
					$this->transaction = null;
					throw $this->exception($throwable);
				}
			}

			/**
			 * @inheritdoc
			 */
			public function start(): IDriver {
				($this->transaction = $this->session->transaction())->begin();
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function commit(): IDriver {
				try {
					$this->transaction->commit();
					$this->transaction = null;
				} catch (\Throwable $throwable) {
					$this->exception($throwable);
				}
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function rollback(): IDriver {
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
				return $this;
			}

			protected function exception(\Throwable $throwable): \Throwable {
				if (stripos($message = $throwable->getMessage(), 'already exists with label') !== false) {
					return new DuplicateEntryException($message, 0, $throwable);
				} else if (stripos($message, 'must have the property') !== false) {
					return new NullValueException($message, 0, $throwable);
				}
				return new DriverException($message, 0, $throwable);
			}

			/**
			 * @param ICrateSchemaQuery $crateSchemaQuery
			 *
			 * @throws \Throwable
			 */
			protected function executeCreateSchemaQuery(ICrateSchemaQuery $crateSchemaQuery) {
				if (($schema = $crateSchemaQuery->getSchema())->isRelation()) {
					return;
				}
				$primaryList = null;
				$indexList = null;
				$delimited = $this->delimite($schema->getRealName());
				foreach ($schema->getProperties() as $property) {
					$name = $property->getName();
					$fragment = 'n.' . $this->delimite($name);
					if ($property->isPrimary()) {
						$primaryList[] = $fragment;
					} else if ($property->isUnique()) {
						$this->native('CREATE CONSTRAINT ON (n:' . $delimited . ') ASSERT ' . $fragment . ' IS UNIQUE');
					}
					if ($property->isRequired()) {
						$this->native('CREATE CONSTRAINT ON (n:' . $delimited . ') ASSERT exists(' . $fragment . ')');
					}
				}
				if ($indexList) {
					$this->native('CREATE INDEX ON :' . $delimited . '(' . implode(',', $indexList) . ')');
				}
				if ($primaryList) {
					$this->native('CREATE CONSTRAINT ON (n:' . $delimited . ') ASSERT (' . implode(', ', $primaryList) . ') IS NODE KEY');
				}
			}

			/**
			 * @param IQueryQueue $queryQueue
			 *
			 * @throws \Exception
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
					$primary = $entity->getPrimary();
					$cypher = 'MERGE (a:' . $this->delimite($schema->getRealName()) . ' {' . $this->delimite($primary->getName()) . ': $primary})';
					$cypher .= " SET a = \$set\n";
					$this->native($cypher, [
						'primary' => $primary->get(),
						'set'     => $this->schemaManager->sanitize($schema, $entity->toArray()),
					]);
				}
				foreach ($entityQueue->getQueries() as $query) {
					$this->execute($query);
				}
			}

			/**
			 * @param IDeleteQuery $deleteQuery
			 *
			 * @throws \Throwable
			 */
			protected function executeDeleteQuery(IDeleteQuery $deleteQuery) {
				$entity = $deleteQuery->getEntity();
				$primary = $entity->getPrimary();
				$cypher = 'MATCH (n:' . $this->delimite($entity->getSchema()->getRealName()) . ' {' . $this->delimite($primary->getName()) . ': $a}) DETACH DELETE n';
				$this->native($cypher, ['a' => $primary->get()]);
			}

			/**
			 * @param IDetachQuery $detachQuery
			 *
			 * @throws \Throwable
			 */
			protected function executeDetachQuery(IDetachQuery $detachQuery) {
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
				$params = [
					'a' => $primary[0]->get(),
					'b' => $primary[1]->get(),
				];
				$cypher = 'MATCH (:' . $this->delimite($entity[0]->getSchema()->getRealName()) . " {" . $this->delimite($primary[0]->getName()) . ': $a})';
				$cypher .= '-[r:' . $this->delimite($detachQuery->getRelation()->getSchema()->getRealName()) . ']';
				$cypher .= '->(:' . $this->delimite($entity[1]->getSchema()->getRealName()) . " {" . $this->delimite($primary[1]->getName()) . ': $b})';
				if ($detachQuery->hasWhere()) {
					$cypher .= ' WHERE' . ($query = $this->fragmentWhereGroup($detachQuery->getWhere()))->getQuery();
					$params = array_merge($params, $query->getParams());
				}
				$cypher .= ' DELETE r';
				$this->native($cypher, $params);
			}

			/**
			 * @param ILinkQuery $linkQuery
			 *
			 * @throws \Throwable
			 */
			protected function executeLinkQuery(ILinkQuery $linkQuery) {
				$this->link(
					$linkQuery->getEntity(),
					$linkQuery->getTo(),
					$linkQuery->getLink()->getName()
				);
			}

			/**
			 * @param IUnlinkQuery $unlinkQuery
			 *
			 * @throws \Throwable
			 */
			protected function executeUnlinkQuery(IUnlinkQuery $unlinkQuery) {
				$link = $unlinkQuery->getLink();
				$primary = $unlinkQuery->getEntity()->getPrimary();
				$cypher = 'MATCH (:' . $this->delimite($link->getFrom()->getRealName()) . " {" . $this->delimite($primary->getName()) . ': $a})-';
				$cypher .= '[r:' . ($unlinkQuery->getLink()->getName()) . ']';
				$cypher .= '->(:' . $this->delimite($link->getTo()->getRealName()) . ')';
				$cypher .= ' DELETE r';
				$this->native($cypher, ['a' => $primary->get()]);
			}

			/**
			 * @param IRelationQuery $relationQuery
			 *
			 * @throws \Throwable
			 */
			protected function executeRelationQuery(IRelationQuery $relationQuery) {
				$using = $relationQuery->getUsing();
				$this->link(
					$relationQuery->getEntity(),
					$relationQuery->getTarget(),
					$relationQuery->getRelation()->getSchema()->getRealName(),
					empty($source = $using->toArray()) === false ? $using->sanitize() : null
				);
			}

			/**
			 * @param IEntity    $from
			 * @param IEntity    $to
			 * @param string     $relation
			 * @param array|null $attributes
			 *
			 * @throws \Throwable
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
					$cypher .= ' {';
					$propertyList = [];
					foreach ($attributes as $k => $v) {
						if ($v !== null) {
							$propertyList[] = $this->delimite($k) . ': $' . $this->delimite($parameterId = (sha1($k)));
							$params[$parameterId] = $v;
						}
					}
					$cypher .= implode(', ', $propertyList) . '}';
				}
				$cypher .= ']';
				$cypher .= "->(b)\n";
				$this->native($cypher, $params);
			}

			/**
			 * @param \Edde\Api\Storage\Query\ISelectQuery $selectQuery
			 *
			 * @return mixed
			 * @throws \Throwable
			 */
			protected function executeSelectQuery(ISelectQuery $selectQuery) {
				$cypher = 'MATCH ';
				$matchList = [];
				$params = [];
				$return = $this->delimite($selectQuery->getReturn());
				$cypher .= '(' . ($alias = $this->delimite($current = $selectQuery->getAlias())) . ':' . $this->delimite(($schema = $selectQuery->getSchema())->getRealName()) . ')';
				foreach ($selectQuery->getJoins() as $name => $join) {
					if ($join->isLink()) {
						$link = $schema->getLink($join->getSchema());
						$cypher .= '-[' . $this->delimite($current . '\r') . ':' . $this->delimite($link->getName()) . ']';
						$cypher .= '->(' . ($return = $this->delimite($current = $name)) . ':' . $this->delimite(($schema = $link->getTo()->getSchema())->getRealName()) . ')';
						continue;
					}
					$relation = $schema->getRelation($join->getSchema());
					$cypher .= '-[' . $this->delimite($current . '\r') . ':' . $this->delimite($relation->getSchema()->getRealName()) . ']';
					$cypher .= '->(' . ($return = $this->delimite($current = $name)) . ':' . $this->delimite(($schema = $relation->getTo()->getTo()->getSchema())->getRealName()) . ')';
				}
				if ($selectQuery->hasWhere()) {
					$cypher .= ' WHERE' . ($query = $this->fragmentWhereGroup($selectQuery->getWhere()))->getQuery();
					$params = $query->getParams();
				}
				$cypher .= implode(', ', $matchList) . ' RETURN ' . $return;
				if ($selectQuery->hasOrder()) {
					$orderList = [];
					foreach ($selectQuery->getOrders() as $column => $asc) {
						$name = $alias;
						if (($dot = strpos($column, '.')) !== false) {
							$name = $this->delimite(substr($column, 0, $dot)) . '.' . $this->delimite(substr($column, $dot + 1));
						}
						$orderList[] = $name . ' ' . ($asc ? 'ASC' : 'DESC');
					}
					$cypher .= 'ORDER BY ' . implode(', ', $orderList);
				}
				return $this->native($cypher, $params);
			}

			/**
			 * @param IWhere $where
			 *
			 * @return INativeQuery
			 * @throws DriverQueryException
			 * @throws \Exception
			 */
			protected function fragmentWhere(IWhere $where): INativeQuery {
				list($operator, $type) = $params = $where->getWhere();
				switch ($operator) {
					case '=':
						$name = $this->delimite($params[2]);
						if (($dot = strpos($params[2], '.')) !== false) {
							$name = $this->delimite(substr($params[2], 0, $dot)) . '.' . $this->delimite(substr($params[2], $dot + 1));
						}
						switch ($type) {
							case 'value':
								return new NativeQuery($name . ' ' . $operator . ' $' . $this->delimite($parameterId = sha1($name . $operator)), [
									$parameterId => $params[3],
								]);
						}
						throw new DriverQueryException(sprintf('Unknown where operator [%s] target [%s].', get_class($where), $type));
					default:
						throw new DriverQueryException(sprintf('Unknown where type [%s] for clause [%s].', $operator, get_class($where)));
				}
			}

			/**
			 * @inheritdoc
			 */
			public function delimite(string $delimite): string {
				return '`' . str_replace('`', '``', $delimite) . '`';
			}

			protected function handleSetup(): void {
				parent::handleSetup();
				$this->session = GraphDatabase::driver($this->url)->session();
			}
		}
