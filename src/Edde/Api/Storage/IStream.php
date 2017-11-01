<?php
	namespace Edde\Api\Storage;

		use Edde\Api\Query\INativeQuery;
		use IteratorAggregate;

		/**
		 * Quite strange name for a stream from the storage; stream means
		 * stream of arrays read by stream (iterator) approach, not as a
		 * bunch of data in the memory.
		 */
		interface IStream extends IteratorAggregate {
			/**
			 * return query powering this stream
			 *
			 * @return INativeQuery
			 */
			public function getQuery(): INativeQuery;

			/**
			 * @return IStream
			 */
			public function execute(): IStream;

			/**
			 * @return \Traversable|array
			 */
			public function getIterator();
		}
