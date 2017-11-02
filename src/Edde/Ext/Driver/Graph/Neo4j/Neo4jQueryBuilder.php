<?php
	declare(strict_types=1);
	namespace Edde\Ext\Driver\Graph\Neo4j;

		use Edde\Api\Node\INode;
		use Edde\Api\Query\Exception\QueryBuilderException;
		use Edde\Api\Query\ICrateSchemaQuery;
		use Edde\Api\Query\INativeQuery;
		use Edde\Api\Query\INativeTransaction;
		use Edde\Common\Query\AbstractQueryBuilder;
		use Edde\Common\Query\NativeQuery;
		use Edde\Common\Query\NativeTransaction;

		class Neo4jQueryBuilder extends AbstractQueryBuilder {
			protected function fragmentCreateSchema(ICrateSchemaQuery $crateSchemaQuery) : INativeQuery {
				/**
				 * relations should not be physically created
				 */
				if (($schema = $crateSchemaQuery->getSchema())->isRelation()) {
					return new NativeQuery('');
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
				$query = '';
				if ($indexList) {
					$query .= 'CREATE INDEX ON :' . $delimited . '(' . implode(',', $indexList) . ");\n";
				}
				if ($primaryList) {
					$query .= 'CREATE CONSTRAINT ON (n:' . $delimited . ') ASSERT (' . implode(', ', $primaryList) . ") IS NODE KEY;\n";
				}
				foreach ($uniqueList as $unique) {
					$query .= 'CREATE CONSTRAINT ON (n:' . $delimited . ') ASSERT ' . $unique . " IS UNIQUE;\n";
				}
				foreach ($requiredList as $required) {
					$query .= 'CREATE CONSTRAINT ON (n:' . $delimited . ') ASSERT exists(' . $required . ");\n";
				}
				return new NativeQuery($query);
			}

			protected function fragmentInsert(INode $root) : INativeQuery {
				$set = [];
				foreach ($root->getNode('set-list')->getNodeList() as $node) {
					$set = array_merge($set, $node->getAttributeList()->array());
				}
				return new NativeQuery('CREATE (n:' . $this->delimite($root->getAttribute('name')) . ' $set)', ['set' => $set]);
			}

			/**
			 * @param INode $root
			 *
			 * @return INativeTransaction
			 * @throws QueryBuilderException
			 */
			protected function fragmentUpdate(INode $root) : INativeQuery {
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
				return (new NativeTransaction())->query(new NativeQuery($cypher, $parameterList));
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
