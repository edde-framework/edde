<?php
	namespace Edde\Common\Query\Fragment;

		use Edde\Common\Node\Node;

		class WhereRelationFragment extends AbstractFragment {
			public function and (): WhereFragment {
				return $this->createRelation(__FUNCTION__);
			}

			public function or (): WhereFragment {
				return $this->createRelation(__FUNCTION__);
			}

			protected function createRelation(string $relation): WhereFragment {
				$this->node->setAttribute('relation', $relation);
				if (($root = $this->node->getParent())->getAttribute('type') !== 'group') {
					$root = $this->root->getNode('where-list');
				}
				$root->addNode($node = new Node('where'));
				return new WhereFragment($this->root, $node);
			}

			public function end(): WhereRelationFragment {
				return new WhereRelationFragment($this->root, $this->node->getParent());
			}
		}
