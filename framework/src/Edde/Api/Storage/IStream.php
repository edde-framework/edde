<?php
	declare(strict_types=1);
	namespace Edde\Api\Storage;

	use Edde\Api\Storage\Exception\InvalidSourceException;
	use Edde\Api\Storage\Exception\StreamException;
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
		 * @param ISelectQuery $query
		 *
		 * @return IStream
		 */
		public function query(ISelectQuery $query): IStream;

		/**
		 * return query powering this stream
		 *
		 * @return ISelectQuery
		 */
		public function getQuery(): ISelectQuery;

		/**
		 * translate raw input from a storage to expected output format
		 *
		 * @param array $source
		 *
		 * @return array
		 * @throws InvalidSourceException
		 */
		public function emit(array $source): array;

		/**
		 * @return \Traversable|array
		 * @throws InvalidSourceException
		 * @throws StreamException
		 */
		public function getIterator();
	}
