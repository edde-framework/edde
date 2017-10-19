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

			public function table(string $name, string $alias = null): TableFragment {
				$this->init();
				$this->node->addNode($node = new Node('table', $name, $alias ? ['alias' => $alias] : []));
				return new TableFragment($this->node, $node);
			}

			public function where(): WhereFragment {
				$this->init();
				$this->node->addNode($node = new Node('where'));
				return new WhereFragment($this->node, $node);
			}

			public function getQuery(): INode {
				$this->init();
				return $this->node;
			}

			protected function handleInit(): void {
				parent::handleInit();
				$this->node = new Node('select');
			}
		}
