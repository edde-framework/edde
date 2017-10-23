<?php
	namespace Edde\Common\Query\Fragment;

		use Edde\Common\Node\Node;

		class OrderFragment extends AbstractFragment {
			public function asc(string $column): OrderFragment {
				$this->node->addNode(new Node('order', null, [
					'column' => $column,
					'asc'    => true,
				]));
				return $this;
			}

			public function desc(string $column): OrderFragment {
				$this->node->addNode(new Node('order', null, [
					'column' => $column,
					'asc'    => false,
				]));
				return $this;
			}
		}
