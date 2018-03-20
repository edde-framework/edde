<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Config\IConfigurable;
	use Edde\Driver\DriverException;
	use Edde\Driver\DuplicateTableException;
	use Edde\Query\IQuery;

	interface IStorage extends IConfigurable {
		/**
		 * start a transaction on the storage
		 *
		 * @param bool $exclusive
		 *
		 * @return IStorage
		 *
		 * @throws TransactionException
		 */
		public function start(bool $exclusive = false): IStorage;

		/**
		 * commit a transaction on storage
		 *
		 * @return IStorage
		 *
		 * @throws TransactionException
		 */
		public function commit(): IStorage;

		/**
		 * rollback a transaction on storage
		 *
		 * @return IStorage
		 *
		 * @throws TransactionException
		 */
		public function rollback(): IStorage;

		/**
		 * execute the callback in a transaction and return it's result
		 *
		 * @param callable $callback
		 *
		 * @return mixed
		 */
		public function transaction(callable $callback);

		/**
		 * execute the given query; query will be translated into native query
		 *
		 * @param IQuery $query
		 *
		 * @return IStream
		 *
		 * @throws StorageException
		 *
		 * @throws DuplicateTableException
		 * @throws DriverException
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
		 * @throws DriverException
		 */
		public function fetch($query, array $params = []);

		/**
		 * @param mixed $query
		 * @param array $params
		 *
		 * @return mixed native driver result
		 *
		 * @throws DriverException
		 */
		public function exec($query, array $params = []);
	}
