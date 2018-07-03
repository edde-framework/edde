<?php
	declare(strict_types = 1);

	namespace Edde\Common\Query;

	use Edde\Api\Node\INode;
	use Edde\Api\Query\IQuery;
	use Edde\Common\Deffered\AbstractDeffered;

	abstract class AbstractQuery extends AbstractDeffered implements IQuery {
		/**
		 * @var INode
		 */
		protected $node;

		public function getNode() {
			$this->use();
			return $this->node;
		}

		public function optimize() {
			return $this;
		}
	}
