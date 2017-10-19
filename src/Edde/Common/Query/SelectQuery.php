<?php
	namespace Edde\Common\Query;

		use Edde\Api\Node\INode;
		use Edde\Common\Node\Node;
		use Edde\Common\Query\Fragment\TableFragment;
		use Edde\Common\Query\Fragment\WhereFragment;

		class SelectQuery extends AbstractQuery {
			/**
			 * @var INode
			 */
			protected $node;

			public function table(string $name): TableFragment {
			}

			public function where(): WhereFragment {
			}

			protected function handleInit(): void {
				parent::handleInit();
				$this->node = new Node('select');
			}
		}
