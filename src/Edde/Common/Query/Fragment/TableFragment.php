<?php
	namespace Edde\Common\Query\Fragment;

		class TableFragment extends AbstractFragment {
			public function column(string $name): TableFragment {
				return $this;
			}

			public function all(): TableFragment {
				return $this;
			}

			public function table(string $name): TableFragment {
			}

			public function where(): WhereFragment {
			}
		}
