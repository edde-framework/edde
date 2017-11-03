<?php
	declare(strict_types=1);
	namespace Edde\Api\Storage;

		use Edde\Api\Query\IQuery;
		use IteratorAggregate;

		/**
		 * Quite strange name for a stream from the storage; stream means
		 * stream of arrays read by stream (iterator) approach, not as a
		 * bunch of data in the memory.
		 */
		interface IStream extends IteratorAggregate {
			/**
			 * return query powering this stream; only select queries could
			 * be used
			 *
			 * @return IQuery
			 */
			public function getQuery(): IQuery;

			/**
			 * @return \Traversable|array
			 */
			public function getIterator();
		}
