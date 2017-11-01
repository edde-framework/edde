<?php
	declare(strict_types=1);
	namespace Edde\Common\Query\Fragment;

		use Edde\Api\Query\Fragment\IWhereFragment;
		use Edde\Api\Query\Fragment\IWhereRelation;
		use Edde\Common\Node\Node;

		class WhereRelationFragment extends AbstractFragment implements IWhereRelation {
			/**
			 * @inheritdoc
			 */
			public function and (): IWhereFragment {
				return $this->createRelation(__FUNCTION__);
			}

			/**
			 * @inheritdoc
			 */
			public function or (): IWhereFragment {
				return $this->createRelation(__FUNCTION__);
			}

			protected function createRelation(string $relation): IWhereFragment {
				$this->node->setAttribute('relation', $relation);
				if (($root = $this->node->getParent())->getAttribute('type') !== 'group') {
					$root = $this->root->getNode('where-list');
				}
				$root->addNode($node = new Node('where'));
				return new WhereFragment($this->root, $node);
			}
		}
