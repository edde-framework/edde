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
		use Edde\Api\Query\IUpdateRelationQuery;
		use Edde\Api\Storage\Exception\DuplicateEntryException;
		use Edde\Api\Storage\Exception\NullValueException;
		use Edde\Common\Driver\AbstractDriver;
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
			public function native($query, array $parameterList = []) {
				try {
					return (function (Result $result) {
						foreach ($result->getRecords() as $record) {
							/** @var $value Node */
							foreach ($record->values() as $value) {
								yield $value->asArray();
							}
						}
					})($this->session->run($query, $parameterList));
				} catch (\Throwable $throwable) {
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
			 * @param IInsertQuery $insertQuery
			 *
			 * @throws \Throwable
			 */
			protected function executeInsertQuery(IInsertQuery $insertQuery) {
				$this->native('CREATE (n:' . $this->delimite(($schema = $insertQuery->getSchema())->getRealName()) . ' $set)', [
					'set' => $this->schemaManager->sanitize($schema, $insertQuery->getSource()),
				]);
			}

			/**
			 * @param IUpdateRelationQuery $updateRelationQuery
			 *
			 * @throws \Throwable
			 */
			protected function executeUpdateRelationQuery(IUpdateRelationQuery $updateRelationQuery) {
				$relation = $updateRelationQuery->getRelation();
				$cypher = 'MATCH';
				$cypher .= "\n\t(a:" . $this->delimite(($sourceLink = $relation->getSourceLink())->getTargetSchema()->getRealName()) . ')';
				$cypher .= '-[r:' . $this->delimite($relation->getSchema()->getRealName()) . ']->';
				$cypher .= '(b:' . $this->delimite(($targetLink = $relation->getTargetLink())->getTargetSchema()->getRealName()) . ")\n";
				$cypher .= 'WHERE';
				$cypher .= "\n\ta." . ($source = $sourceLink->getTargetProperty()->getName()) . " = \$a AND";
				$cypher .= "\n\tb." . ($target = $targetLink->getTargetProperty()->getName()) . " = \$b\n";
				$cypher .= 'DELETE r';
				$this->native($cypher, [
					'a' => $updateRelationQuery->getFrom()[$source],
					'b' => $updateRelationQuery->getTo()[$target],
				]);
				$this->executeCreateRelationQuery($updateRelationQuery);
			}

			/**
			 * @param ICreateRelationQuery $createRelationQuery
			 *
			 * @throws \Throwable
			 */
			protected function executeCreateRelationQuery(ICreateRelationQuery $createRelationQuery) {
				$relation = $createRelationQuery->getRelation();
				$cypher = 'MATCH';
				$cypher .= "\n\t(a:" . $this->delimite(($sourceLink = $relation->getSourceLink())->getTargetSchema()->getRealName()) . '),';
				$cypher .= "\n\t(b:" . $this->delimite(($targetLink = $relation->getTargetLink())->getTargetSchema()->getRealName()) . ")\n";
				$cypher .= 'WHERE';
				$cypher .= "\n\ta." . ($source = $sourceLink->getTargetProperty()->getName()) . " = \$a AND";
				$cypher .= "\n\tb." . ($target = $targetLink->getTargetProperty()->getName()) . " = \$b\n";
				$cypher .= "MERGE\n\t(a)-[:" . $this->delimite($relation->getSchema()->getRealName());
				$properties = [
					'a' => $createRelationQuery->getFrom()[$source],
					'b' => $createRelationQuery->getTo()[$target],
				];
				if ($createRelationQuery->hasSource()) {
					$cypher .= ' {';
					$propertyList = [];
					foreach ($createRelationQuery->getSource() as $k => $v) {
						if ($v !== null) {
							$propertyList[] = $this->delimite($k) . ': $' . ($parameterId = ('p_' . sha1(random_bytes(42))));
							$properties[$parameterId] = $v;
						}
					}
					$cypher .= implode(', ', $propertyList) . '}';
				}
				$cypher .= ']->(b)';
				$this->native($cypher, $properties);
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
				$table = $selectQuery->getTable();
				$return = $this->delimite($table->getSelect());
				$cypher .= '(' . $this->delimite($current = $table->getAlias()) . ':' . $this->delimite($table->getSchema()->getRealName()) . ')';
				$schema = $table->getSchema();
				foreach ($table->getJoinList() as $name => $relation) {
					$relation = $schema->getRelation($relation);
					$cypher .= '-[' . $this->delimite($current . '\r') . ':' . $this->delimite($relation->getSchema()->getRealName()) . ']-(' . ($return = $this->delimite($current = $name)) . ':' . $this->delimite(($schema = $relation->getTargetLink()->getTargetSchema())->getRealName()) . ')';
				}
				$parameterList = [];
				if ($table->hasWhere()) {
					$cypher .= "\nWHERE" . ($query = $this->fragmentWhereGroup($table->where()))->getQuery() . "\n";
					$parameterList = $query->getParameterList();
				}
				return $this->native($cypher . implode(",\n\t", $matchList) . "\nRETURN\n\t" . $return, $parameterList);
			}

			/**
			 * @param IUpdateQuery $updateQuery
			 *
			 * @throws \Throwable
			 */
			protected function executeUpdateQuery(IUpdateQuery $updateQuery) {
				$table = $updateQuery->getTable();
				$cypher = "MATCH\n\t(" . ($alias = $this->delimite($table->getAlias())) . ':' . $this->delimite(($schema = $table->getSchema())->getRealName()) . ")\n";
				$parameterList = [];
				if ($table->hasWhere()) {
					$cypher .= 'WHERE' . ($query = $this->fragmentWhereGroup($table->where()))->getQuery() . "\n";
					$parameterList = $query->getParameterList();
				}
				$this->native($cypher . "SET\n\t" . $alias . ' = $set', array_merge($parameterList, [
					'set' => $this->schemaManager->sanitize($schema, $updateQuery->getSource()),
				]));
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
								return new NativeQuery($name . ' ' . $operator . ' $' . ($parameterId = 'p_' . sha1(random_bytes(42))), [
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

			protected function handleSetup(): void {
				parent::handleSetup();
				$this->session = GraphDatabase::driver($this->url)->session();
			}
		}
