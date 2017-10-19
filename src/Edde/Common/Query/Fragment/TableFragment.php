<?php
	namespace Edde\Common\Query\Fragment;

		use Edde\Common\Node\Node;

		class TableFragment extends AbstractFragment {
			public function column(string $name): TableFragment {
				$this->node->addNode(new Node('column', $name));
				return $this;
			}

			public function all(): TableFragment {
				$this->node->setAttribute('all', true);
				return $this;
			}

			public function table(string $name, string $alias = null): TableFragment {
				$this->root->addNode($node = new Node('table', $name, ['alias' => $alias]));
				return new TableFragment($this->root, $node);
			}

			public function where(): WhereFragment {
				$this->root->addNode($node = new Node('where'));
				return new WhereFragment($this->root, $node);
			}
		}
