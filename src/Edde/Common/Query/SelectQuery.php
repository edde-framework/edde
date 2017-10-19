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
			/**
			 * @var WhereFragment
			 */
			protected $whereFragment;

			public function table(string $name, string $alias = null): TableFragment {
				$this->init();
				$this->node->addNode($node = new Node('table', $name, ['alias' => $alias]));
				return new TableFragment($this->node, $node);
			}

			public function where(): WhereFragment {
				if ($this->whereFragment) {
					return $this->whereFragment;
				}
				$this->init();
				$this->node->addNode($node = new Node('where'));
				return $this->whereFragment = new WhereFragment($this->node, $node);
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
