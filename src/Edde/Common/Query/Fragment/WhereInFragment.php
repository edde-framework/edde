<?php
	namespace Edde\Common\Query\Fragment;

		use Edde\Api\Query\IQuery;

		class WhereInFragment extends AbstractFragment {
			public function select(IQuery $query): WhereRelationFragment {
				$this->node->setAttribute('target', 'query');
				$this->node->addNode($query->getQuery());
				return new WhereRelationFragment($this->root, $this->node);
			}
		}
