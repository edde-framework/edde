<?php
	declare(strict_types=1);
	namespace Edde\Common\Query;

		use Edde\Api\Query\Fragment\IOrder;
		use Edde\Api\Query\Fragment\ITable;
		use Edde\Api\Query\Fragment\IWhere;
		use Edde\Api\Query\ISelectQuery;
		use Edde\Api\Schema\ISchema;
		use Edde\Common\Node\Node;
		use Edde\Common\Query\Fragment\OrderFragment;
		use Edde\Common\Query\Fragment\TableFragment;
		use Edde\Common\Query\Fragment\WhereFragment;

		class SelectQuery extends AbstractQuery implements ISelectQuery {
			/**
			 * @var IOrder
			 */
			protected $orderFragment;
			/**
			 * @var ISchema
			 */
			protected $schema;

			public function __construct(ISchema $schema) {
				parent::__construct('select');
				$this->schema = $schema;
			}

			/**
			 * @inheritdoc
			 */
			public function select(ISelectQuery $selectQuery, string $alias): ISelectQuery {
				$this->init();
				$this->node->getNode('column-list')->addNode($node = new Node('column', null, [
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
				$this->node->getNode('table-list')->addNode($node = new Node('table', null, ['table' => $name]));
				$alias ? $node->setAttribute('alias', $alias) : null;
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

			/**
			 * @inheritdoc
			 */
			public function order(): IOrder {
				$this->init();
				return $this->orderFragment ?: $this->orderFragment = new OrderFragment($this->node, $this->node->getNode('order-list'));
			}

			protected function handleInit(): void {
				parent::handleInit();
				$this->node = new Node('select');
				$this->table($this->schema->getName())->all();
			}

			public function __clone() {
				parent::__clone();
				$this->init();
				$this->orderFragment = null;
			}
		}
