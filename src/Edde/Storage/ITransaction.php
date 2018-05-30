<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	/**
	 * This should be used as a service, just to keep connection and
	 * transactions separated even could be inside the same.
	 */
	interface ITransaction {
		/**
		 * start a transaction
		 *
		 * @return ITransaction
		 */
		public function start(): ITransaction;

		/**
		 * commit a transaction
		 *
		 * @return ITransaction
		 *
		 * @throws StorageException
		 */
		public function commit(): ITransaction;

		/**
		 * rollback a transaction
		 *
		 * @return ITransaction
		 *
		 * @throws StorageException
		 */
		public function rollback(): ITransaction;

		/**
		 * run a transaction and return result of callback
		 *
		 * @param callable $callback
		 *
		 * @return mixed
		 *
		 * @throws StorageException
		 */
		public function transaction(callable $callback);
	}