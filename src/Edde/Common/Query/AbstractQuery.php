<?php
	declare(strict_types=1);

	namespace Edde\Common\Query;

	use Edde\Api\Node\INode;
	use Edde\Api\Query\IQuery;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object\Object;

	abstract class AbstractQuery extends Object implements IQuery {
		use ConfigurableTrait;
		/**
		 * @var INode
		 */
		protected $node;

		public function getNode() {
			return $this->node;
		}

		public function optimize() {
			return $this;
		}
	}
