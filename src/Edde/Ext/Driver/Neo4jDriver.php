<?php
	declare(strict_types=1);
	namespace Edde\Ext\Driver;

		use Edde\Api\Driver\Exception\DriverException;
		use Edde\Api\Driver\Exception\DriverQueryException;
		use Edde\Api\Driver\IDriver;
		use Edde\Api\Query\Fragment\IWhere;
		use Edde\Api\Query\ICrateSchemaQuery;
		use Edde\Api\Query\IEntityQueueQuery;
		use Edde\Api\Query\INativeQuery;
		use Edde\Api\Query\ISelectQuery;
		use Edde\Api\Query\ITransactionQuery;
		use Edde\Api\Storage\Exception\DuplicateEntryException;
		use Edde\Api\Storage\Exception\NullValueException;
		use Edde\Common\Driver\AbstractDriver;
		use Edde\Common\Query\EntityQueueQuery;
		use Edde\Common\Query\NativeQuery;
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
				foreach ($schema->getPropertyList() as $property) {
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
			 * @param IEntityQueueQuery $entityQueueQuery
			 *
			 * @throws \Exception
			 * @throws \Throwable
			 */
			protected function executeEntityQueueQuery(IEntityQueueQuery $entityQueueQuery) {
				$entityQueue = $entityQueueQuery->getEntityQueue();
				if ($entityQueue->isEmpty()) {
					return;
				}
				foreach ($entityQueue->getEntityUnlinks() as $entityLink) {
					$cypher = '';
					$link = $entityLink->getLink();
					$primary = $entityLink->getEntity()->getPrimary();
					$cypher .= "MATCH (:" . $this->delimite($link->getFrom()->getRealName()) . " {" . $this->delimite($primary->getName()) . ": \$a})-";
					$cypher .= '[r:' . ($entityLink->getLink()->getName()) . ']';
					$cypher .= "->(:" . $this->delimite($link->getTo()->getRealName()) . ') ';
					$cypher .= 'DELETE r';
					$this->native($cypher, ['a' => $primary->get()]);
				}
				foreach ($entityQueue->getEntities() as $entity) {
					$cypher = null;
					$params = [];
					$schema = $entity->getSchema();
					if ($entity->isDirty() === false || $schema->isRelation()) {
						continue;
					}
					$primary = $entity->getPrimary();
					$primaryId = $primary->get();
					$params[$primaryId] = [
						'primary' => $primaryId,
						'set'     => $this->schemaManager->sanitize($schema, $entity->toArray()),
					];
					$primaryId = $this->delimite($primaryId);
					$cypher .= 'MERGE (' . ($id = $this->delimite($primaryId)) . ':' . $this->delimite($schema->getRealName()) . ' {' . $this->delimite($primary->getName()) . ': $' . $primaryId . '.primary})';
					$cypher .= ' SET ' . $id . ' = $' . $primaryId . ".set\n";
					$this->native($cypher, $params);
				}
				foreach ($entityQueue->getEntityLinks() as $entityLink) {
					$cypher = null;
					$params = [];
					$entity = $entityLink->getEntity();
					$primary = $entity->getPrimary();
					$primaryId = $primary->get();
					$delimitedFrom = $this->delimite($primaryId);
					$params[$primaryId] = $primaryId;
					$cypher .= 'MERGE (' . $delimitedFrom . ':' . $this->delimite($entity->getSchema()->getRealName()) . ' {' . $this->delimite($primary->getName()) . ': $' . $this->delimite($primaryId) . "})\n";
					$entity = $entityLink->getTo();
					$primary = $entity->getPrimary();
					$primaryId = $primary->get();
					$delimitedTo = $this->delimite($primaryId);
					$params[$primaryId] = $primaryId;
					$cypher .= 'MERGE (' . $delimitedTo . ':' . $this->delimite($entity->getSchema()->getRealName()) . ' {' . $this->delimite($primary->getName()) . ': $' . $this->delimite($primaryId) . "})\n";
					$cypher .= 'MERGE (' . $delimitedFrom . ')';
					$cypher .= '-[:' . $this->delimite($entityLink->getLink()->getName()) . ']';
					$cypher .= '->(' . $delimitedTo . ")\n";
					$this->native($cypher, $params);
				}
				foreach ($entityQueue->getEntityRelations() as $entityRelation) {
					$cypher = null;
					$params = [];
					$cypher .= 'MERGE (' . $this->delimite($entityRelation->getEntity()->getPrimary()->get()) . ')';
					$cypher .= '-[:' . $this->delimite($entityRelation->getRelation()->getSchema()->getRealName());
					$using = $entityRelation->getUsing();
					if (empty($source = $using->toArray()) === false) {
						$cypher .= ' {';
						$propertyList = [];
						foreach ($this->schemaManager->sanitize($using->getSchema(), $source) as $k => $v) {
							if ($v !== null) {
								$propertyList[] = $this->delimite($k) . ': $' . $this->delimite($parameterId = (sha1(random_bytes(42))));
								$params[$parameterId] = $v;
							}
						}
						$cypher .= implode(', ', $propertyList) . '}';
					}
					$cypher .= ']';
					$cypher .= '->(' . $this->delimite($entityRelation->getTarget()->getPrimary()->get()) . ")\n";
					$this->native($cypher, $params);
				}
			}

			/**
			 * @param ITransactionQuery $transactionQuery
			 *
			 * @throws \Throwable
			 */
			protected function executeTransactionQuery(ITransactionQuery $transactionQuery) {
				$this->executeEntityQueueQuery(new EntityQueueQuery($transactionQuery->getTransaction()->getEntityQueue()));
			}

			/**
			 * @param ISelectQuery $selectQuery
			 *
			 * @return mixed
			 * @throws \Throwable
			 */
			protected function executeSelectQuery(ISelectQuery $selectQuery) {
				$cypher = "MATCH\n\t";
				$matchList = [];
				$parameterList = [];
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
					$cypher .= "\nWHERE" . ($query = $this->fragmentWhereGroup($selectQuery->getWhere()))->getQuery() . "\n";
					$parameterList = $query->getParameterList();
				}
				$cypher .= implode(",\n\t", $matchList) . "\nRETURN\n\t" . $return;
				if ($selectQuery->hasOrder()) {
					$orderList = [];
					foreach ($selectQuery->getOrders() as $column => $asc) {
						$name = $alias;
						if (($dot = strpos($column, '.')) !== false) {
							$name = $this->delimite(substr($column, 0, $dot)) . '.' . $this->delimite(substr($column, $dot + 1));
						}
						$orderList[] = $name . ' ' . ($asc ? 'ASC' : 'DESC');
					}
					$cypher .= "ORDER BY\n\t" . implode("\n\t,", $orderList);
				}
				return $this->native($cypher, $parameterList);
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
								return new NativeQuery($name . ' ' . $operator . ' $' . $this->delimite($parameterId = sha1(random_bytes(42))), [
									$parameterId => $parameters[3],
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

			/**
			 * @param string     $method
			 * @param string     $path
			 * @param array|null $parameters
			 *
			 * @return \stdClass
			 * @throws DriverException
			 */
			protected function send(string $method, string $path, array $parameters = null) {
				$json = json_encode((object)$parameters);
				$length = $parameters ? strlen($json) : 0;
				$result = file_get_contents(strpos($path, 'http') === 0 ? $path : ($this->url . $path), false, stream_context_create([
					'http' => [
						'method'  => $method,
						'header'  => "X-Stream: true\r\nContent-Type: application/json\r\nContent-Length: $length\r\n",
						'content' => $json,
					],
				]));
				if (is_string($result)) {
					$result = json_decode($result);
					foreach ($result->errors ?? [] as $error) {
						throw new DriverException($error->message);
					}
					return $result;
				}
				throw new DriverException(sprintf('Communication problem, kaboom!'));
			}

			protected function handleSetup(): void {
				parent::handleSetup();
				$this->session = GraphDatabase::driver($this->url)->session();
			}
		}
