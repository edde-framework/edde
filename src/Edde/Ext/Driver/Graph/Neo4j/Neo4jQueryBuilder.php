<?php
	namespace Edde\Ext\Driver\Graph\Neo4j;

		use Edde\Api\Node\INode;
		use Edde\Api\Query\Exception\QueryBuilderException;
		use Edde\Api\Query\INativeQuery;
		use Edde\Common\Query\AbstractQueryBuilder;
		use Edde\Common\Query\NativeQuery;

		class Neo4jQueryBuilder extends AbstractQueryBuilder {
			protected function fragmentInsert(INode $node): INativeQuery {
				$parameterList = $this->fragmentParameterList($node->getNode('parameter-list'))->getParameterList();
				$create = [];
				foreach ($node->getNode('column-list')->getNodeList() as $node) {
					$create[$node->getAttribute('column')] = $parameterList[$node->getAttribute('parameter')];
				}
				return new NativeQuery('CREATE (n:Foooo) SET n = $create', [
					'create' => $create,
				]);
			}

			/**
			 * @inheritdoc
			 */
			public function delimite(string $delimite): string {
				return $delimite;
			}

			/**
			 * @inheritdoc
			 */
			public function type(string $type): string {
				throw new QueryBuilderException(sprintf('Unknown type [%s] in query builder [%s]', $type, static::class));
			}
		}
