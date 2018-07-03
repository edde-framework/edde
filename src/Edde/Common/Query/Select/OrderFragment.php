<?php
	declare(strict_types=1);

	namespace Edde\Common\Query\Select;

	use Edde\Common\Node\Node;
	use Edde\Common\Query\AbstractFragment;

	class OrderFragment extends AbstractFragment {
		/**
		 * @var string
		 */
		protected $order = 'asc';

		public function asc() {
			$this->order = 'asc';
			return $this;
		}

		public function desc() {
			$this->order = 'desc';
			return $this;
		}

		/**
		 * order a property of a schema; it's something similar to a column
		 *
		 * @param string      $property
		 * @param string|null $prefix
		 * @param string|null $alias
		 *
		 * @return $this
		 */
		public function property($property, $prefix = null, $alias = null) {
			$this->node->addNode(new Node('property', $property, [
				'alias'  => $alias,
				'prefix' => $prefix,
				'order'  => $this->order,
			]));
			return $this;
		}
	}
