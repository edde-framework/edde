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
				$root = $this->root;
				if ($this->node->getAttribute('group')) {
					/**
					 * first parent should be where-list, second where itself
					 */
					$root = $this->node->getParent()->getParent();
				}
				$root->getNode('where-list')->addNode($node = new Node('where'));
				return new WhereFragment($this->root, $node);
			}

			public function end(): WhereRelationFragment {
				return new WhereRelationFragment($this->root, $this->node->getParent()->getParent());
			}
		}
