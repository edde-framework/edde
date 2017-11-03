<?php
	declare(strict_types=1);
	namespace Edde\Ext\Driver\Graph\Neo4j;

		use Edde\Api\Query\Exception\QueryBuilderException;
		use Edde\Api\Query\Fragment\ISchemaFragment;
		use Edde\Api\Query\Fragment\IWhere;
		use Edde\Api\Query\Fragment\IWhereTo;
		use Edde\Api\Query\IRelationQuery;
		use Edde\Api\Query\ISelectQuery;
		use Edde\Api\Query\ITransactionQuery;
		use Edde\Common\Object\Object;

		class Neo4jQueryBuilder extends Object {
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
					$matchList[] = ($query = $this->fragment($schemaFragment))->getQuery();
					if ($schemaFragment->isSelected()) {
						$returnList[] = $schemaFragment->getAlias();
					}
					$parameterList = array_merge($parameterList, $query->getParameterList());
				}
				$cypher .= implode(",\n\t", $matchList) . "\nRETURN\n\t" . implode(', ', $returnList);
				return new TransactionQuery($cypher, $parameterList);
			}

			protected function fragmentLink(ISchemaFragment $schemaFragment): ITransactionQuery {
				$cypher = '(a:' . $schemaFragment->getSchema()->getName() . ')';
				foreach ($schemaFragment->getLinkList() as $alias => $link) {
					$relation = $link->getRelation();
					$cypher .= '-[:' . $this->delimite($relation->getSchema()->getName()) . ']->(b:' . $this->delimite($relation->getTargetLink()->getTargetSchema()->getName()) . ')';
				}
				$cypher .= "\nWHERE\n\t";
				foreach ($schemaFragment->getLinkList() as $alias => $link) {
					$relation = $link->getRelation();
					$cypher .= 'a.' . $this->delimite($relation->getTargetLink()->getTargetProperty()->getName()) . ' = $guid';
				}
				return new TransactionQuery($cypher);
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
