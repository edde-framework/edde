<?php
	namespace Edde\Common\Query\Fragment;

		class WhereFragment extends AbstractFragment {
			public function eq(string $name): WhereToFragment {
			}

			public function neq(string $name): WhereToFragment {
			}

			public function gt(string $name): WhereThenFragment {
			}

			public function gte(string $name): WhereThenFragment {
			}

			public function lt(string $name): WhereThenFragment {
			}

			public function lte(string $name): WhereThenFragment {
			}

			public function in(string $name): WhereInFragment {
			}

			public function where(): WhereFragment {
			}
		}
