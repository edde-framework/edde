<?php
	namespace Edde\Common\Query\Fragment;

		use Edde\Api\Query\Fragment\ITable;
		use Edde\Common\Node\Node;

		class TableFragment extends AbstractFragment implements ITable {
			/**
			 * @var OrderFragment
			 */
			protected $orderFragment;

			/**
			 * @inheritdoc
			 */
			public function all(): ITable {
				$this->root->getNode('column-list')->addNode($node = new Node('column', null, ['type' => 'asterisk']));
				$node->setAttribute('prefix', $this->node->getAttribute('alias', $this->node->getValue()));
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function column(string $name, string $alias = null): ITable {
				$this->root->getNode('column-list')->addNode($node = new Node('column', $name, ['type' => 'column']));
				if (($alis = $this->node->getAttribute('alias')) !== null) {
					$node->setAttribute('prefix', $alis);
				}
				if ($alias) {
					$node->setAttribute('alias', $alias);
				}
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function table(string $name, string $alias = null): ITable {
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
