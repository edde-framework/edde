<?php
	namespace Edde\Common\Query\Fragment;

		use Edde\Api\Query\IQuery;

		class WhereInFragment extends AbstractFragment {
			public function values(array $values): WhereFragment {
			}

			public function select(IQuery $query): WhereFragment {
			}
		}
