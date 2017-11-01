<?php
	declare(strict_types=1);
	namespace Edde\Common\Query\Fragment;

		use Edde\Api\Query\Fragment\IOrder;
		use Edde\Common\Node\Node;

		class OrderFragment extends AbstractFragment implements IOrder {
			/**
			 * @inheritdoc
			 */
			public function asc(string $column): IOrder {
				$this->node->addNode(new Node('order', null, [
					'column' => $column,
					'asc'    => true,
				]));
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function desc(string $column): IOrder {
				$this->node->addNode(new Node('order', null, [
					'column' => $column,
					'asc'    => false,
				]));
				return $this;
			}
		}
