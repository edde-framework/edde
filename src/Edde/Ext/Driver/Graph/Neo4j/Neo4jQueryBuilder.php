<?php
	declare(strict_types=1);
	namespace Edde\Ext\Driver\Graph\Neo4j;

		use Edde\Api\Node\INode;
		use Edde\Api\Query\Exception\QueryBuilderException;
		use Edde\Api\Query\ICrateSchemaQuery;
		use Edde\Api\Query\IInsertQuery;
		use Edde\Api\Query\INativeTransaction;
		use Edde\Api\Query\ISelectQuery;
		use Edde\Api\Query\ITransactionQuery;
		use Edde\Common\Query\AbstractQueryBuilder;
		use Edde\Common\Query\NativeQuery;
		use Edde\Common\Query\TransactionQuery;

		class Neo4jQueryBuilder extends AbstractQueryBuilder {
			protected function fragmentCreateSchema(ICrateSchemaQuery $crateSchemaQuery) : ITransactionQuery {
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

			protected function fragmentInsert(IInsertQuery $insertQuery) : ITransactionQuery {
				$source = $this->schemaManager->sanitize(($schema = $insertQuery->getSchema()), $insertQuery->getSource());
				return new TransactionQuery('CREATE (n:' . $this->delimite($schema->getName()) . ' $set)', ['set' => $source]);
			}

			/**
			 * @param ISelectQuery $selectQuery
			 *
			 * @return ITransactionQuery
			 */
			protected function fragmentSelect(ISelectQuery $selectQuery) : ITransactionQuery {
				$returnList = [];
				$cypher = "MATCH\n\t";
				$matchList = [];
				foreach ($selectQuery->getSchemaFragmentList() as $schemaFragment) {
					$matchList[] = '(' . $this->delimite($alias = $schemaFragment->getAlias()) . ':' . $this->delimite($schemaFragment->getSchema()->getName()) . ')';
					if ($schemaFragment->isSelected()) {
						$returnList[] = $alias;
					}
				}
				$cypher .= implode(",\n\t", $matchList) . "\nRETURN\n\t" . implode(', ', $returnList);
				return new TransactionQuery($cypher);
			}

			/**
			 * @param INode $root
			 *
			 * @return INativeTransaction
			 * @throws QueryBuilderException
			 */
			protected function fragmentUpdate(INode $root) : ITransactionQuery {
				$set = [];
				foreach ($root->getNode('set-list')->getNodeList() as $node) {
					$set = array_merge($set, $node->getAttributeList()->array());
				}
				$cypher = "MATCH\n\t(" . ($alias = $root->getAttribute('alias')) . ':' . $this->delimite($root->getAttribute('name')) . ")\n";
				if ($root->hasNode('where-list')) {
					$cypher .= "WHERE\n" . ($query = $this->fragmentWhereList($root->getNode('where-list')))->getQuery() . "\n";
					$parameterList = $query->getParameterList();
				}
				$parameterList['set'] = $set;
				$cypher .= "SET\n\t" . $alias . ' = $set';
				return new TransactionQuery($cypher, $parameterList);
			}

			/**
			 * @inheritdoc
			 */
			public function delimite(string $delimite) : string {
				return '`' . str_replace('`', '``', $delimite) . '`';
			}

			/**
			 * @inheritdoc
			 */
			public function type(string $type) : string {
				throw new QueryBuilderException(sprintf('Unknown type [%s] in query builder [%s]', $type, static::class));
			}
		}
