<?php
	namespace Edde\Common\Query\Fragment;

		use Edde\Api\Node\NodeException;
		use Edde\Api\Query\IQuery;
		use Edde\Common\Node\Node;

		class TableFragment extends AbstractFragment {
			/**
			 * @var OrderFragment
			 */
			protected $orderFragment;

			public function column(string $name): TableFragment {
				$this->root->getNode('column-list')->addNode($node = new Node('column', $name, ['type' => 'column']));
				if (($alis = $this->node->getAttribute('alias')) !== null) {
					$node->setAttribute('prefix', $alis);
				}
				return $this;
			}

			/**
			 * @param IQuery $query
			 * @param string $alias
			 *
			 * @return TableFragment
			 * @throws NodeException
			 */
			public function select(IQuery $query, string $alias): TableFragment {
				$this->root->getNode('column-list')->addNode($node = new Node('column', null, [
					'type'  => 'query',
					'alias' => $alias,
				]));
				$node->addNode($query->getQuery());
				return $this;
			}

			public function all(): TableFragment {
				$this->root->getNode('column-list')->addNode($node = new Node('column', null, ['type' => 'asterisk']));
				$node->setAttribute('prefix', $this->node->getAttribute('alias', $this->node->getValue()));
				return $this;
			}

			public function table(string $name, string $alias = null): TableFragment {
				$this->root->getNode('table-list')->addNode($node = new Node('table', $name, $alias ? ['alias' => $alias] : []));
				return new TableFragment($this->root, $node);
			}

			public function where(): WhereFragment {
				$this->root->getNode('where-list')->addNode($node = new Node('where'));
				return new WhereFragment($this->root, $node);
			}

			public function order(): OrderFragment {
				return $this->orderFragment ?: $this->orderFragment = new OrderFragment($this->root, $this->root->getNode('order-list'));
			}
		}
