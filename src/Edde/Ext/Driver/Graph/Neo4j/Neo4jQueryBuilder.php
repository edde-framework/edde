<?php
	declare(strict_types=1);
	namespace Edde\Ext\Driver\Graph\Neo4j;

		use Edde\Api\Node\INode;
		use Edde\Api\Query\Exception\QueryBuilderException;
		use Edde\Api\Query\ICrateSchemaQuery;
		use Edde\Api\Query\IInsertQuery;
		use Edde\Api\Query\INativeTransaction;
		use Edde\Api\Query\ITransactionQuery;
		use Edde\Common\Query\AbstractQueryBuilder;
		use Edde\Common\Query\NativeQuery;
		use Edde\Common\Query\NativeTransaction;
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
				return new TransactionQuery('CREATE (n:' . $this->delimite($insertQuery->getSchema()->getName()) . ' $set)', ['set' => $insertQuery->getSource()]);
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
			 * @param INode $root
			 *
			 * @return INativeTransaction
			 * @throws QueryBuilderException
			 */
			protected function fragmentSelect(INode $root) : INativeTransaction {
				$parameterList = [];
				$cypher = null;
				$alias = null;
				if ($root->hasNode('table-list')) {
					foreach ($root->getNode('table-list')->getNodeList() as $node) {
						$cypher .= "MATCH\n\t(" . ($alias = $node->getAttribute('alias')) . ':' . $this->delimite($node->getAttribute('table')) . ")\n";
					}
				}
				if ($root->hasNode('where-list')) {
					$query = $this->fragmentWhereList($root->getNode('where-list'));
					$cypher .= "WHERE\n" . $query->getQuery() . "\n";
					$parameterList = $query->getParameterList();
				}
				return (new NativeTransaction())->query(new NativeQuery($cypher . 'RETURN ' . $alias, $parameterList));
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
