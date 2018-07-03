<?php
	declare(strict_types = 1);

	namespace Edde\Api\Iterator;

	use Iterator;

	/**
	 * Common iterator with a few aditional features.
	 */
	interface IIterator extends Iterator {
		/**
		 * @param bool $continue
		 *
		 * @return IIterator
		 */
		public function setContinue(bool $continue = true): IIterator;

		/**
		 * @param bool $skipNext
		 *
		 * @return IIterator
		 */
		public function setSkipNext(bool $skipNext = true): IIterator;
	}
