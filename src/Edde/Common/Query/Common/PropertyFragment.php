<?php
	declare(strict_types=1);

	namespace Edde\Common\Query\Common;

	use Edde\Common\Node\Node;
	use Edde\Common\Query\AbstractFragment;

	/**
	 * Universal fragment which will generate appropriate node under it's parent node.
	 */
	class PropertyFragment extends AbstractFragment {
		/**
		 * select property of a schema; it's something similar to a column
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
			]));
			return $this;
		}

		/**
		 * quote input if it is needed; input is literal string
		 *
		 * @param string $quote
		 *
		 * @return $this
		 */
		public function quote($quote) {
			$this->node->addNode(new Node('quote', $quote));
			return $this;
		}

		/**
		 * input is parameter; parameters can be extracted and used for bound (in case of RDBM supporting parameter binding)
		 *
		 * @param mixed       $parameter
		 * @param string|null $name
		 *
		 * @return $this
		 */
		public function parameter($parameter, $name = null) {
			$this->node->addNode(new Node('parameter', $parameter, [
				'name' => $name ?: hash('sha256', spl_object_hash($this)),
			]));
			return $this;
		}

		/**
		 * enable add arbitrary fragment to the parent
		 *
		 * @param AbstractFragment $abstractFragment
		 *
		 * @return $this
		 */
		public function fragment(AbstractFragment $abstractFragment) {
			$this->node->addNode($abstractFragment->getNode());
			return $this;
		}
	}
