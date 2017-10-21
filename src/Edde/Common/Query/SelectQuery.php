<?php
	namespace Edde\Common\Query;

		use Edde\Common\Node\Node;
		use Edde\Common\Query\Fragment\TableFragment;
		use Edde\Common\Query\Fragment\WhereFragment;

		class SelectQuery extends AbstractQuery {
			public function table(string $name, string $alias = null): TableFragment {
				$this->init();
				$this->node->getNode('table-list')->addNode($node = new Node('table', $name, $alias ? ['alias' => $alias] : []));
				return new TableFragment($this->node, $node);
			}

			public function where(): WhereFragment {
				$this->init();
				$this->node->getNode('where-list')->addNode($node = new Node('where'));
				return new WhereFragment($this->node, $node);
			}

			protected function handleInit(): void {
				parent::handleInit();
				$this->node = new Node('select');
			}
		}
