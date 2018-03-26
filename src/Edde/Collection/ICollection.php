<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	use IteratorAggregate;
	use Traversable;

	/**
	 * A collection is read-only result of some (usually selection) query.
	 */
	interface ICollection extends IteratorAggregate {
		/**
		 * @return Traversable|IRecord[]
		 */
		public function getIterator();
	}
