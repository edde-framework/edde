<?php
	namespace Edde\Common\Query\Fragment;

		use Edde\Api\Node\NodeException;
		use Edde\Api\Query\IQuery;
		use Edde\Common\Node\Node;

		class TableFragment extends AbstractFragment {
			public function column(string $name): TableFragment {
				$this->node->addNode(new Node('column', $name, ['type' => 'column']));
				return $this;
			}

			/**
			 * @param IQuery $query
			 *
			 * @return TableFragment
			 * @throws NodeException
			 */
			public function select(IQuery $query, string $alias): TableFragment {
				$this->node->addNode($node = new Node('column', null, [
					'type'  => $query,
					'alias' => $alias,
				]));
				$node->addNode($query->getQuery());
				return $this;
			}

			public function all(): TableFragment {
				$this->node->setAttribute('all', true);
				return $this;
			}

			public function table(string $name, string $alias = null): TableFragment {
				$this->root->addNode($node = new Node('table', $name, $alias ? ['alias' => $alias] : []));
				return new TableFragment($this->root, $node);
			}

			public function where(): WhereFragment {
				$this->root->addNode($node = new Node('where'));
				return new WhereFragment($this->root, $node);
			}
		}
