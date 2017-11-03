<?php
	declare(strict_types=1);
	namespace Edde\Ext\Driver\Graph\Neo4j;

		use Edde\Api\Query\Exception\QueryBuilderException;
		use Edde\Api\Query\Fragment\ISchemaFragment;
		use Edde\Api\Query\ICreateRelationQuery;
		use Edde\Api\Query\ISelectQuery;
		use Edde\Api\Query\ITransactionQuery;
		use Edde\Common\Object\Object;

		class Neo4jQueryBuilder extends Object {
			protected function fragmentRelation(ICreateRelationQuery $relationQuery): ITransactionQuery {
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

		}
