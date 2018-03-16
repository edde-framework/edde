<?php
	declare(strict_types=1);
	namespace Edde\Api\Storage;

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\Storage\Exception\DuplicateTableException;
	use Edde\Api\Storage\Exception\ExclusiveTransactionException;
	use Edde\Api\Storage\Exception\NoTransactionException;
	use Edde\Api\Storage\Exception\StorageException;
	use Edde\Api\Storage\Query\IQuery;
	use Edde\Exception\Driver\DriverException;

	interface IStorage extends IConfigurable {
		/**
		 * start a transaction on the storage
		 *
		 * @param bool $exclusive
		 *
		 * @return IStorage
		 * @throws ExclusiveTransactionException
		 */
		public function start(bool $exclusive = false): IStorage;

		/**
		 * commit a transaction on storage
		 *
		 * @return IStorage
		 * @throws NoTransactionException
		 */
		public function commit(): IStorage;

		/**
		 * rollback a transaction on storage
		 *
		 * @return IStorage
		 * @throws NoTransactionException
		 */
		public function rollback(): IStorage;

		/**
		 * execute the given query; query will be translated into native query
		 *
		 * @param IQuery $query
		 *
		 * @return IStream
		 *
		 * @throws StorageException
		 * @throws DuplicateTableException
		 * @throws \Edde\Exception\Driver\DriverException
		 */
		public function execute(IQuery $query);

		/**
		 * prepare stream for the given query
		 *
		 * @param IQuery $query
		 *
		 * @return IStream
		 */
		public function stream(IQuery $query): IStream;

		/**
		 * execute the given native query and return stream as a result
		 *
		 * @param mixed $query
		 * @param array $params
		 *
		 * @return mixed native driver result
		 *
		 * @throws \Edde\Exception\Driver\DriverException
		 */
		public function fetch($query, array $params = []);

		/**
		 * @param mixed $query
		 * @param array $params
		 *
		 * @return mixed native driver result
		 *
		 * @throws \Edde\Exception\Driver\DriverException
		 */
		public function exec($query, array $params = []);
	}
