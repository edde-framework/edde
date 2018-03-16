<?php
	declare(strict_types=1);
	namespace Edde\Storage\Query\Fragment;

	use Edde\Storage\Query\IFragment;
	use IteratorAggregate;
	use Traversable;

	interface IWhereGroup extends IFragment, IteratorAggregate {
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
		 * @return Traversable|IWhere[]
		 */
		public function getIterator();
	}
