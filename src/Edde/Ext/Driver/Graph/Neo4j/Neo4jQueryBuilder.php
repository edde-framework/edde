<?php
	namespace Edde\Ext\Driver\Graph\Neo4j;

		use Edde\Api\Node\INode;
		use Edde\Api\Query\Exception\QueryBuilderException;
		use Edde\Api\Query\ICrateSchemaQuery;
		use Edde\Api\Query\INativeTransaction;
		use Edde\Common\Query\AbstractQueryBuilder;
		use Edde\Common\Query\NativeQuery;
		use Edde\Common\Query\NativeTransaction;

		class Neo4jQueryBuilder extends AbstractQueryBuilder {
			protected function fragmentCreateSchema(ICrateSchemaQuery $crateSchemaQuery): INativeTransaction {
				$nativeTransaction = new NativeTransaction();
				/**
				 * relations should not be physically created
				 */
				if (($schema = $crateSchemaQuery->getSchema())->isRelation()) {
					return $nativeTransaction;
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
				if ($indexList) {
					$nativeTransaction->query(new NativeQuery('CREATE INDEX ON :' . $delimited . '(' . implode(',', $indexList) . ')'));
				}
				if ($primaryList) {
					$nativeTransaction->query(new NativeQuery('CREATE CONSTRAINT ON (n:' . $delimited . ') ASSERT (' . implode(', ', $primaryList) . ') IS NODE KEY'));
				}
				foreach ($uniqueList as $unique) {
					$nativeTransaction->query(new NativeQuery('CREATE CONSTRAINT ON (n:' . $delimited . ') ASSERT ' . $unique . ' IS UNIQUE'));
				}
				foreach ($requiredList as $required) {
					$nativeTransaction->query(new NativeQuery('CREATE CONSTRAINT ON (n:' . $delimited . ') ASSERT exists(' . $required . ')'));
				}
				return $nativeTransaction;
			}

			protected function fragmentRelation(INode $root): INativeTransaction {
				list($alpha, $beta) = $root->getNode('relation-list')->getNodeList();
				$cypher = "MATCH\n\t";
				$parameterList = [
					'a' => $alpha->getAttribute('value'),
					'b' => $beta->getAttribute('value'),
				];
				$cypher .= '(a:' . $this->delimite($alpha->getAttribute('schema')) . ' {' . $this->delimite($alpha->getAttribute('property')) . ': $a}),';
				$cypher .= '(b:' . $this->delimite($beta->getAttribute('schema')) . ' {' . $this->delimite($beta->getAttribute('property')) . ": \$b})\n";
				$cypher .= "CREATE UNIQUE\n\t(a)-[:" . $this->delimite($root->getAttribute('name')) . ' $set]->(b)';
				foreach ($root->getNode('set-list')->getNodeList() as $node) {
					foreach ($node->getAttributeList()->array() as $k => $v) {
						$parameterList['set'][$k] = $v;
					}
				}
				return new NativeTransaction($cypher, $parameterList);
			}

			protected function fragmentInsert(INode $root): INativeTransaction {
				$set = [];
				foreach ($root->getNode('set-list')->getNodeList() as $node) {
					$set = array_merge($set, $node->getAttributeList()->array());
				}
				return new NativeTransaction('CREATE (n:' . $this->delimite($root->getAttribute('name')) . ' $set)', [
					'set' => $set,
				]);
			}

			/**
			 * @param INode $root
			 *
			 * @return INativeTransaction
			 * @throws QueryBuilderException
			 */
			protected function fragmentUpdate(INode $root): INativeTransaction {
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
				return new NativeTransaction($cypher, $parameterList);
			}

			/**
			 * @param INode $root
			 *
			 * @return INativeTransaction
			 * @throws QueryBuilderException
			 */
			protected function fragmentSelect(INode $root): INativeTransaction {
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
				return new NativeTransaction($cypher . 'RETURN ' . $alias, $parameterList);
			}

			/**
			 * @param INode $root
			 *
			 * @return INativeTransaction
			 * @throws QueryBuilderException
			 * @throws \Exception
			 */
			protected function fragmentWhere(INode $root): INativeTransaction {
				$where = null;
				static $expressions = [
					'eq'  => '=',
					'neq' => '!=',
					'gt'  => '>',
					'gte' => '>=',
					'lt'  => '<',
					'lte' => '<=',
				];
				$parameterList = [];
				if (isset($expressions[$type = $root->getAttribute('type')])) {
					$where = ($prefix = $root->getAttribute('prefix')) ? $prefix . '.' : '';
					$where .= $this->delimite($root->getAttribute('where')) . ' ' . $expressions[$type] . ' ';
					switch ($target = $root->getAttribute('target', 'column')) {
						case 'column':
							$where .= $this->delimite($root->getAttribute('column'));
							break;
						case 'parameter':
							$parameterList[$id = ('p_' . sha1(random_bytes(64)))] = $root->getAttribute('parameter');
							$where .= '$' . $id;
							break;
						default:
							throw new QueryBuilderException(sprintf('Unknown where target type [%s] in [%s].', $target, static::class));
					}
					return new NativeTransaction($where, $parameterList);
				}
				switch ($type) {
					case 'group':
						return new NativeTransaction("(\n" . ($query = $this->fragmentWhereList($root))->getQuery() . "\t)", $query->getParameterList());
				}
				throw new QueryBuilderException(sprintf('Unknown where type [%s].', $type));
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
