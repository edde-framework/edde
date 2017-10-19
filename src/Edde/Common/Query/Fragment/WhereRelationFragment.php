<?php
	namespace Edde\Common\Query\Fragment;

		class WhereRelationFragment extends AbstractFragment {
			public function and (): WhereFragment {
				$this->node->setAttribute('relation', 'and');
				return new WhereFragment($this->root, $this->node->getParent());
			}

			public function or (): WhereFragment {
				$this->node->setAttribute('relation', 'and');
				return new WhereFragment($this->root, $this->node->getParent());
			}

			public function end(): WhereRelationFragment {
				return new WhereRelationFragment($this->root, $this->node->getParent()->getParent());
			}
		}
