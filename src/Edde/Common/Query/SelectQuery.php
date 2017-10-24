<?php
	namespace Edde\Common\Query;

		use Edde\Api\Query\Fragment\ITable;
		use Edde\Api\Query\Fragment\IWhere;
		use Edde\Api\Query\ISelectQuery;
		use Edde\Common\Node\Node;
		use Edde\Common\Query\Fragment\TableFragment;
		use Edde\Common\Query\Fragment\WhereFragment;

		class SelectQuery extends AbstractQuery implements ISelectQuery {
			/**
			 * @inheritdoc
			 */
			public function select(ISelectQuery $selectQuery, string $alias): ISelectQuery {
				$this->root->getNode('column-list')->addNode($node = new Node('column', null, [
					'type'  => 'query',
					'alias' => $alias,
				]));
				$node->addNode($selectQuery->getQuery());
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function table(string $name, string $alias = null): ITable {
				$this->init();
				$this->node->getNode('table-list')->addNode($node = new Node('table', $name, $alias ? ['alias' => $alias] : []));
				return new TableFragment($this->node, $node);
			}

			/**
			 * @inheritdoc
			 */
			public function where(): IWhere {
				$this->init();
				$this->node->getNode('where-list')->addNode($node = new Node('where'));
				return new WhereFragment($this->node, $node);
			}

			protected function handleInit(): void {
				parent::handleInit();
				$this->node = new Node('select');
			}
		}
