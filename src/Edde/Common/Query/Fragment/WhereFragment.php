<?php
	namespace Edde\Common\Query\Fragment;

		use Edde\Common\Node\Node;

		class WhereFragment extends AbstractFragment {
			public function eq(string $name): WhereToFragment {
				return $this->createToFragment(__FUNCTION__, $name);
			}

			public function neq(string $name): WhereToFragment {
				return $this->createToFragment(__FUNCTION__, $name);
			}

			public function gt(string $name): WhereThanFragment {
				return $this->createThanFragment(__FUNCTION__, $name);
			}

			public function gte(string $name): WhereThanFragment {
				return $this->createThanFragment(__FUNCTION__, $name);
			}

			public function lt(string $name): WhereThanFragment {
				return $this->createThanFragment(__FUNCTION__, $name);
			}

			public function lte(string $name): WhereThanFragment {
				return $this->createThanFragment(__FUNCTION__, $name);
			}

			public function in(string $name): WhereInFragment {
				$this->node->setAttribute('type', 'in');
				$this->node->setAttribute('where', $name);
				return new WhereInFragment($this->root, $this->node);
			}

			public function group(): WhereFragment {
				$this->node->addNode($node = new Node('where', null, ['group' => true]));
				return new WhereFragment($this->root, $node);
			}

			public function and (): WhereFragment {
				return $this->createRelation(__FUNCTION__);
			}

			public function or (): WhereFragment {
				return $this->createRelation(__FUNCTION__);
			}

			protected function createRelation(string $relation): WhereFragment {
				$this->node->setAttribute('relation-to', $relation);
				return new WhereFragment($this->root, $this->node);
			}

			protected function createToFragment(string $type, string $name): WhereToFragment {
				$this->node->setAttribute('type', $type);
				$this->node->setAttribute('where', $name);
				return new WhereToFragment($this->root, $this->node);
			}

			protected function createThanFragment(string $type, string $name): WhereThanFragment {
				$this->node->setAttribute('type', $type);
				$this->node->setAttribute('where', $name);
				return new WhereThanFragment($this->root, $this->node);
			}
		}
