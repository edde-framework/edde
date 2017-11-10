<?php
	declare(strict_types=1);
	namespace Edde\Api\Storage;

		use Edde\Api\Storage\Query\ISelectQuery;
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
			 * @param \Edde\Api\Storage\Query\ISelectQuery $query
			 *
			 * @return IStream
			 */
			public function query(ISelectQuery $query): IStream;

			/**
			 * return query powering this stream
			 *
			 * @return \Edde\Api\Storage\Query\ISelectQuery
			 */
			public function getQuery(): ISelectQuery;

			/**
			 * @return \Traversable|array
			 */
			public function getIterator();
		}
