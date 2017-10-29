<?php
	namespace Edde\Ext\Driver\Graph\Neo4j;

		use Edde\Api\Node\INode;
		use Edde\Api\Query\Exception\QueryBuilderException;
		use Edde\Api\Query\INativeBatch;
		use Edde\Common\Query\AbstractQueryBuilder;
		use Edde\Common\Query\NativeBatch;

		class Neo4jQueryBuilder extends AbstractQueryBuilder {
			protected function fragmentCreateSchema(INode $root): INativeBatch {
				$nativeBatch = new NativeBatch();
				$primaryList = null;
				$indexList = null;
				$uniqueList = [];
				$requiredList = [];
				$delimited = $this->delimite($root->getAttribute('name'));
				foreach ($root->getNodeList() as $node) {
					$name = $node->getAttribute('name');
					$property = 'n.' . $this->delimite($name);
					if ($node->getAttribute('primary', false)) {
						$primaryList[] = $property;
					} else if ($node->getAttribute('unique', false)) {
						$uniqueList[] = $property;
					}
					if ($node->getAttribute('required', false)) {
						$requiredList[] = $property;
					}
				}
				if ($indexList) {
					$nativeBatch->addQuery('CREATE INDEX ON :' . $delimited . '(' . implode(',', $indexList) . ')');
				}
				if ($primaryList) {
					$nativeBatch->addQuery('CREATE CONSTRAINT ON (n:' . $delimited . ') ASSERT (' . implode(', ', $primaryList) . ') IS NODE KEY');
				}
				foreach ($uniqueList as $unique) {
					$nativeBatch->addQuery('CREATE CONSTRAINT ON (n:' . $delimited . ') ASSERT ' . $unique . ' IS UNIQUE');
				}
				foreach ($requiredList as $required) {
					$nativeBatch->addQuery('CREATE CONSTRAINT ON (n:' . $delimited . ') ASSERT exists(' . $required . ')');
				}
				return $nativeBatch;
			}

			protected function fragmentInsert(INode $root): INativeBatch {
				$parameterList = $this->fragmentParameterList($root->getNode('parameter-list'))->getParameterList();
				$create = [];
				foreach ($root->getNode('column-list')->getNodeList() as $node) {
					$create[$node->getAttribute('column')] = $parameterList[$node->getAttribute('parameter')];
				}
				return new NativeBatch('CREATE (n:' . $this->delimite($root->getAttribute('table')) . ' $create)', [
					'create' => $create,
				]);
			}

			/**
			 * @param INode $root
			 *
			 * @return INativeBatch
			 * @throws QueryBuilderException
			 */
			protected function fragmentUpdate(INode $root): INativeBatch {
				$parameterList = $this->fragmentParameterList($root->getNode('parameter-list'))->getParameterList();
				$update = [];
				foreach ($root->getNode('column-list')->getNodeList() as $node) {
					$update[$node->getAttribute('column')] = $parameterList[$parameter = $node->getAttribute('parameter')];
					unset($parameterList[$parameter]);
				}
				$parameterList['update'] = $update;
				$cypher[] = 'MATCH (' . ($alias = $root->getAttribute('alias')) . ':' . $this->delimite($root->getAttribute('table')) . ")\n";
				if ($root->hasNode('where-list')) {
					$cypher[] = "WHERE\n";
					$query = $this->fragmentWhereList($root->getNode('where-list'));
					$cypher[] = $query->getQuery();
					$parameterList = array_merge($parameterList, $query->getParameterList());
				}
				$cypher[] = 'SET ' . $alias . ' = $update';
				return new NativeBatch(implode('', $cypher), $parameterList);
			}

			/**
			 * @param INode $root
			 *
			 * @return INativeBatch
			 * @throws QueryBuilderException
			 */
			protected function fragmentWhere(INode $root): INativeBatch {
				$where = null;
				static $expressions = [
					'eq'  => '=',
					'neq' => '!=',
					'gt'  => '>',
					'gte' => '>=',
					'lt'  => '<',
					'lte' => '<=',
				];
				if (isset($expressions[$type = $root->getAttribute('type')])) {
					$where = ($prefix = $root->getAttribute('prefix')) ? $prefix . '.' : '';
					$where .= $this->delimite($root->getAttribute('where')) . ' ' . $expressions[$type] . ' ';
					switch ($root->getAttribute('target', 'column')) {
						case 'column':
							$where .= $this->delimite($root->getAttribute('column'));
							break;
						case 'parameter':
							$where .= '$' . $root->getAttribute('parameter');
							break;
					}
					return new NativeBatch($where);
				}
				switch ($type) {
					case 'group':
						return new NativeBatch("(\n" . ($query = $this->fragmentWhereList($root))->getQuery() . "\t)", $query->getParameterList());
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
