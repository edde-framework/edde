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
			 * override query of the stream
			 *
			 * @param IQuery $query
			 *
			 * @return IStream
			 */
			public function query(IQuery $query): IStream;

			/**
			 * return query powering this stream
			 *
			 * @return IQuery
			 */
			public function getQuery(): IQuery;

			/**
			 * @return \Traversable|array
			 */
			public function getIterator();
		}
