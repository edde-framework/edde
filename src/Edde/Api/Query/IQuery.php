<?php
	declare(strict_types=1);

	namespace Edde\Api\Query;

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\Node\INode;

	/**
	 * Basic interface for the Edde's Intermediate Query Structure.
	 */
	interface IQuery extends IConfigurable {
		/**
		 * optimize inner structure; IQL enables to use duplicate elements (select -> ... and so) - this duplicates
		 * are removed by this method
		 *
		 * @return $this
		 */
		public function optimize();

		/**
		 * return internal IQS node
		 *
		 * @return INode
		 */
		public function getNode();
	}
