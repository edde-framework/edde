<?php
	declare(strict_types=1);
	namespace Edde\Api\Storage\Query\Fragment;

	use Edde\Api\Storage\Query\IFragment;

	interface IWhereGroup extends IFragment, \IteratorAggregate {
		/**
		 * where and relation
		 *
		 * @return IWhere
		 */
		public function and (): IWhere;

		/**
		 * where or relation
		 *
		 * @return IWhere
		 */
		public function or (): IWhere;

		/**
		 * @return \Traversable|IWhere[]
		 */
		public function getIterator();
	}
