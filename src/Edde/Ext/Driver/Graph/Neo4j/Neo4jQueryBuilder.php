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
				$delimited = $this->delimite($root->getAttribute('name'));
				foreach ($root->getNodeList() as $node) {
					$name = $node->getAttribute('name');
					if ($node->getAttribute('primary', false)) {
						$primaryList[] = 'n.' . $this->delimite($name);
					} else if ($node->getAttribute('unique', false)) {
						$uniqueList[] = 'n.' . $this->delimite($name);
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
