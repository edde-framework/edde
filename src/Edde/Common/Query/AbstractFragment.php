<?php
	declare(strict_types=1);

	namespace Edde\Common\Query;

	use Edde\Api\Node\INode;
	use Edde\Common\Object;

	abstract class AbstractFragment extends Object {
		/**
		 * @var INode
		 */
		protected $node;

		/**
		 * @param INode $node
		 */
		public function __construct(INode $node = null) {
			$this->node = $node;
		}

		public function getNode() {
			return $this->node;
		}
	}
