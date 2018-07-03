<?php
	declare(strict_types = 1);

	namespace Edde\Common\Query;

	use Edde\Api\Node\INode;
	use Edde\Common\AbstractObject;

	abstract class AbstractFragment extends AbstractObject {
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
