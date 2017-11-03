<?php
	declare(strict_types=1);
	namespace Edde\Ext\Driver\Graph\Neo4j;

		use Edde\Api\Driver\Exception\DriverException;
		use Edde\Api\Driver\Exception\DriverQueryException;
		use Edde\Api\Driver\IDriver;
		use Edde\Api\Query\Fragment\IWhereGroup;
		use Edde\Api\Query\Fragment\IWhereTo;
		use Edde\Api\Query\ICrateSchemaQuery;
		use Edde\Api\Query\ICreateRelationQuery;
		use Edde\Api\Query\IInsertQuery;
		use Edde\Api\Query\INativeQuery;
		use Edde\Api\Query\ISelectQuery;
		use Edde\Api\Query\IUpdateQuery;
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
				$delimited = $this->delimite($schema->getName());
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
				$this->native('CREATE (n:' . $this->delimite(($schema = $insertQuery->getSchema())->getName()) . ' $set)', [
					'set' => $this->schemaManager->sanitize($schema, $insertQuery->getSource()),
				]);
			}

			/**
			 * @param ICreateRelationQuery $relationQuery
			 *
			 * @throws \Throwable
			 */
			protected function executeCreateRelationQuery(ICreateRelationQuery $relationQuery) {
				$relation = $relationQuery->getRelation();
				$cypher = 'MATCH';
				$cypher .= "\n\t(a:" . $this->delimite(($sourceLink = $relation->getSourceLink())->getTargetSchema()->getName()) . '),';
				$cypher .= "\n\t(b:" . $this->delimite(($targetLink = $relation->getTargetLink())->getTargetSchema()->getName()) . ")\n";
				$cypher .= 'WHERE';
				$cypher .= "\n\ta." . ($source = $sourceLink->getTargetProperty()->getName()) . " = \$a AND";
				$cypher .= "\n\tb." . ($target = $targetLink->getTargetProperty()->getName()) . " = \$b\n";
				$cypher .= "MERGE\n\t(a)-[:" . $this->delimite($relation->getSchema()->getName()) . ']->(b)';
				$this->native($cypher, [
					'a' => $relationQuery->getFrom()[$source],
					'b' => $relationQuery->getTo()[$target],
				]);
			}

			/**
			 * @param ISelectQuery $selectQuery
			 *
			 * @return mixed
			 * @throws \Throwable
			 */
			protected function executeSelectQuery(ISelectQuery $selectQuery) {
				$returnList = [];
				$cypher = "MATCH\n\t";
				$matchList = [];
				$parameterList = [];
				foreach ($selectQuery->getTableList() as $table) {
					$alias = $this->delimite($table->getAlias());
					if ($table->isSelected()) {
						$returnList[] = $alias;
					}
					switch ($type = $table->getType()) {
						case 'Table':
							$cypher .= '(' . $alias . ':' . $this->delimite($table->getSchema()->getName()) . ')';
							$parameterList = [];
							foreach ($table->getJoinList() as $name => $relation) {
								$cypher .= '-[:' . $this->delimite($relation->getSchema()->getName()) . ']-(' . ($return = $this->delimite($name)) . ':' . $this->delimite($relation->getTargetLink()->getTargetSchema()->getName()) . ')';
							}
							if ($table->hasWhere()) {
								$cypher .= "\nWHERE" . ($query = $this->fragmentWhereGroup($table->where()))->getQuery() . "\n";
								$parameterList = array_merge($parameterList, $query->getParameterList());
							}
							break;
						default:
							throw new DriverQueryException(sprintf('Unknown schema fragment type [%s].', $type));
					}
				}
				return $this->native($cypher . implode(",\n\t", $matchList) . "\nRETURN\n\t" . implode(', ', $returnList), $parameterList);
			}

			/**
			 * @param IUpdateQuery $updateQuery
			 *
			 * @throws \Throwable
			 */
			protected function executeUpdateQuery(IUpdateQuery $updateQuery) {
				$table = $updateQuery->getTable();
				$cypher = "MATCH\n\t(" . ($alias = $this->delimite($table->getAlias())) . ':' . $this->delimite(($schema = $table->getSchema())->getName()) . ")\n";
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
			 * @param IWhereGroup $whereGroup
			 *
			 * @return INativeQuery
			 * @throws DriverException
			 */
			protected function fragmentWhereGroup(IWhereGroup $whereGroup): INativeQuery {
				$cypher = null;
				$parameterList = [];
				foreach ($whereGroup as $where) {
					$fragment = "\n\t";
					if ($cypher) {
						$fragment = ' ' . strtoupper($where->getRelation()) . "\n\t";
					}
					$cypher .= $fragment . ($query = $this->fragment($where->getExpression()))->getQuery();
					$parameterList = array_merge($parameterList, $query->getParameterList());
				}
				return new NativeQuery($cypher, $parameterList);
			}

			/**
			 * @param IWhereTo $whereTo
			 *
			 * @return INativeQuery
			 * @throws \Exception
			 */
			protected function fragmentWhereTo(IWhereTo $whereTo): INativeQuery {
				$name = $this->delimite($whereTo->getTable()->getAlias()) . '.' . $this->delimite($whereTo->getName());
				switch ($target = $whereTo->getTarget()) {
					case 'column':
						list($prefix, $column) = $whereTo->getValue();
						return new NativeQuery($name . ' = ' . $this->delimite($whereTo->getAlias() ?: $prefix) . '.' . $this->delimite($column));
					case 'value':
						return new NativeQuery($name . ' = $' . ($parameterId = 'p_' . sha1($target . microtime(true) . random_bytes(8))), [
							$parameterId => $whereTo->getValue(),
						]);
				}
				throw new DriverQueryException(sprintf('Unknown where expression [%s] target [%s].', $whereTo->getType(), $target));
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
