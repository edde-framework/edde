<?php
	namespace Edde\Common\Query\Fragment;

		class WhereRelationFragment extends AbstractFragment {
			public function and (): WhereFragment {
				$this->node->setAttribute('relation', 'and');
			}

			public function or (): WhereFragment {
				$this->node->setAttribute('relation', 'and');
			}

			public function end(): WhereRelationFragment {
			}
		}
