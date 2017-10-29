<?php
	namespace Edde\Ext\Driver\Graph\Neo4j;

		use Edde\Api\Node\INode;
		use Edde\Api\Query\Exception\QueryBuilderException;
		use Edde\Api\Query\INativeQuery;
		use Edde\Common\Query\AbstractQueryBuilder;
		use Edde\Common\Query\NativeQuery;

		class Neo4jQueryBuilder extends AbstractQueryBuilder {
			protected function fragmentCreateSchema(INode $root): INativeQuery {
				$primaryList = null;
				foreach ($root->getNodeList() as $node) {
					$name = $node->getAttribute('name');
					if ($node->getAttribute('primary', false)) {
						$primaryList[] = $this->delimite($name);
					}
				}
				if ($primaryList) {
					$index = 'CREATE INDEX ON :' . $this->delimite($root->getAttribute('name')) . '(' . implode(', ', $primaryList) . ')';
				}
			}

			protected function fragmentInsert(INode $root): INativeQuery {
				$parameterList = $this->fragmentParameterList($root->getNode('parameter-list'))->getParameterList();
				$create = [];
				foreach ($root->getNode('column-list')->getNodeList() as $node) {
					$create[$node->getAttribute('column')] = $parameterList[$node->getAttribute('parameter')];
				}
				return new NativeQuery('CREATE (n:' . $this->delimite($root->getAttribute('table')) . ' $create)', [
					'create' => $create,
				]);
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
