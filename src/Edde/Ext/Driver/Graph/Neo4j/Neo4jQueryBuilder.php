<?php
	declare(strict_types=1);
	namespace Edde\Ext\Driver\Graph\Neo4j;

		use Edde\Api\Query\Exception\QueryBuilderException;
		use Edde\Api\Query\Fragment\IWhere;
		use Edde\Api\Query\Fragment\IWhereGroup;
		use Edde\Api\Query\Fragment\IWhereTo;
		use Edde\Api\Query\ICrateSchemaQuery;
		use Edde\Api\Query\IInsertQuery;
		use Edde\Api\Query\IRelationQuery;
		use Edde\Api\Query\ISelectQuery;
		use Edde\Api\Query\ITransactionQuery;
		use Edde\Api\Query\IUpdateQuery;
		use Edde\Common\Query\AbstractQueryBuilder;
		use Edde\Common\Query\NativeQuery;
		use Edde\Common\Query\TransactionQuery;

		class Neo4jQueryBuilder extends AbstractQueryBuilder {
			protected function fragmentCreateSchema(ICrateSchemaQuery $crateSchemaQuery): ITransactionQuery {
				/**
				 * relations should not be physically created
				 */
				if (($schema = $crateSchemaQuery->getSchema())->isRelation()) {
					return new TransactionQuery();
				}
				$primaryList = null;
				$indexList = null;
				$uniqueList = [];
				$requiredList = [];
				$delimited = $this->delimite($schema->getName());
				foreach ($schema->getPropertyList() as $property) {
					$name = $property->getName();
					$fragment = 'n.' . $this->delimite($name);
					if ($property->isPrimary()) {
						$primaryList[] = $fragment;
					} else if ($property->isUnique()) {
						$uniqueList[] = $fragment;
					}
					if ($property->isRequired()) {
						$requiredList[] = $fragment;
					}
				}
				$transactionQuery = new TransactionQuery();
				if ($indexList) {
					$transactionQuery->query(new NativeQuery('CREATE INDEX ON :' . $delimited . '(' . implode(',', $indexList) . ')'));
				}
				if ($primaryList) {
					$transactionQuery->query(new NativeQuery('CREATE CONSTRAINT ON (n:' . $delimited . ') ASSERT (' . implode(', ', $primaryList) . ') IS NODE KEY'));
				}
				foreach ($uniqueList as $unique) {
					$transactionQuery->query(new NativeQuery('CREATE CONSTRAINT ON (n:' . $delimited . ') ASSERT ' . $unique . ' IS UNIQUE'));
				}
				foreach ($requiredList as $required) {
					$transactionQuery->query(new NativeQuery('CREATE CONSTRAINT ON (n:' . $delimited . ') ASSERT exists(' . $required . ')'));
				}
				return $transactionQuery;
			}

			protected function fragmentInsert(IInsertQuery $insertQuery): ITransactionQuery {
				$source = $this->schemaManager->sanitize(($schema = $insertQuery->getSchema()), $insertQuery->getSource());
				return new TransactionQuery('CREATE (n:' . $this->delimite($schema->getName()) . ' $set)', ['set' => $source]);
			}

			protected function fragmentRelation(IRelationQuery $relationQuery): ITransactionQuery {
				$relation = $relationQuery->getRelation();
				$relationName = $relation->getSchema()->getName();
				$cypher = 'MATCH';
				$cypher .= "\n\t(a:" . $this->delimite(($sourceLink = $relation->getSourceLink())->getTargetSchema()->getName()) . '),';
				$cypher .= "\n\t(b:" . $this->delimite(($targetLink = $relation->getTargetLink())->getTargetSchema()->getName()) . ")\n";
				$cypher .= 'WHERE';
				$cypher .= "\n\ta." . ($source = $sourceLink->getTargetProperty()->getName()) . " = \$a AND";
				$cypher .= "\n\tb." . ($target = $targetLink->getTargetProperty()->getName()) . " = \$b\n";
				$cypher .= "MERGE\n\t(a)-[:" . $this->delimite($relationName) . ']->(b)';
				return new TransactionQuery($cypher, [
					'a' => $relationQuery->getFrom()[$source],
					'b' => $relationQuery->getTo()[$target],
				]);
			}

			/**
			 * @param ISelectQuery $selectQuery
			 *
			 * @return ITransactionQuery
			 * @throws QueryBuilderException
			 */
			protected function fragmentSelect(ISelectQuery $selectQuery): ITransactionQuery {
				$returnList = [];
				$cypher = "MATCH\n\t";
				$matchList = [];
				$parameterList = [];
				foreach ($selectQuery->getSchemaFragmentList() as $schemaFragment) {
					$match = '(' . $this->delimite($alias = $schemaFragment->getAlias()) . ':' . $this->delimite($schemaFragment->getSchema()->getName()) . ')';
					if ($schemaFragment->isSelected()) {
						$returnList[] = $alias;
					}
					if ($schemaFragment->hasWhere()) {
						$match .= "\nWHERE" . ($query = $this->fragmentWhereGroup($schemaFragment->where()))->getQuery() . "\n";
						$parameterList = array_merge($parameterList, $query->getParameterList());
					}
					$matchList[] = $match;
				}
				$cypher .= implode(",\n\t", $matchList) . "\nRETURN\n\t" . implode(', ', $returnList);
				return new TransactionQuery($cypher, $parameterList);
			}

			/**
			 * @param IUpdateQuery $updateQuery
			 *
			 * @return ITransactionQuery
			 * @throws QueryBuilderException
			 */
			protected function fragmentUpdate(IUpdateQuery $updateQuery): ITransactionQuery {
				$schemaFragment = $updateQuery->getSchemaFragment();
				$cypher = "MATCH\n\t(" . ($alias = $this->delimite($schemaFragment->getAlias())) . ':' . $this->delimite(($schema = $schemaFragment->getSchema())->getName()) . ")\n";
				$parameterList = [];
				if ($schemaFragment->hasWhere()) {
					$cypher .= 'WHERE' . ($query = $this->fragmentWhereGroup($schemaFragment->where()))->getQuery() . "\n";
					$parameterList = $query->getParameterList();
				}
				$cypher .= "SET\n\t" . $alias . ' = $set';
				return new TransactionQuery($cypher, array_merge($parameterList, [
					'set' => $this->schemaManager->sanitize($schema, $updateQuery->getSource()),
				]));
			}

			/**
			 * @param IWhereGroup $whereGroup
			 *
			 * @return ITransactionQuery
			 * @throws QueryBuilderException
			 */
			protected function fragmentWhereGroup(IWhereGroup $whereGroup): ITransactionQuery {
				$whereList = null;
				$parameterList = [];
				foreach ($whereGroup as $where) {
					$sql = "\n\t";
					if ($whereList) {
						$sql = ' ' . strtoupper($where->getRelation()) . "\n\t";
					}
					$whereList .= $sql . ($query = $this->fragmentWhere($where))->getQuery();
					$parameterList = array_merge($parameterList, $query->getParameterList());
				}
				return new TransactionQuery($whereList, $parameterList);
			}

			/**
			 * @param IWhere $where
			 *
			 * @return ITransactionQuery
			 * @throws QueryBuilderException
			 */
			protected function fragmentWhere(IWhere $where): ITransactionQuery {
				return $this->fragment($where->getExpression());
			}

			/**
			 * @param IWhereTo $whereTo
			 *
			 * @return ITransactionQuery
			 * @throws QueryBuilderException
			 * @throws \Exception
			 */
			protected function fragmentWhereExpressionEq(IWhereTo $whereTo): ITransactionQuery {
				$name = $this->delimite($whereTo->getSchemaFragment()->getAlias()) . '.' . $this->delimite($whereTo->getName());
				switch ($target = $whereTo->getTarget()) {
					case 'column':
						list($prefix, $column) = $whereTo->getValue();
						return new TransactionQuery($name . ' = ' . $this->delimite($prefix) . '.' . $this->delimite($column));
					case 'value':
						return new TransactionQuery($name . ' = $' . ($parameterId = 'p_' . sha1($target . microtime(true) . random_bytes(8))), [
							$parameterId => $whereTo->getValue(),
						]);
				}
				throw new QueryBuilderException(sprintf('Unknown where expression [%s] target [%s].', $whereTo->getType(), $target));
			}

			/**
			 * @inheritdoc
			 */
			public function delimite(string $delimite): string {
				return '`' . str_replace('`', '``', $delimite) . '`';
			}

			/**
			 * @inheritdoc
			 */
			public function type(string $type): string {
				throw new QueryBuilderException(sprintf('Unknown type [%s] in query builder [%s]', $type, static::class));
			}
		}
