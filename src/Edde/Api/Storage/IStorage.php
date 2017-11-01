<?php
	namespace Edde\Api\Storage;

		use Edde\Api\Config\IConfigurable;
		use Edde\Api\Query\INativeQuery;
		use Edde\Api\Query\INativeTransaction;
		use Edde\Api\Query\IQuery;

		interface IStorage extends IConfigurable {
			/**
			 * start a transaction on the storage
			 *
			 * @param bool $exclusive
			 *
			 * @return IStorage
			 */
			public function start(bool $exclusive = false): IStorage;

			/**
			 * commit a transaction on storage
			 *
			 * @return IStorage
			 */
			public function commit(): IStorage;

			/**
			 * rollback a transaction on storage
			 *
			 * @return IStorage
			 */
			public function rollback(): IStorage;

			/**
			 * executes a transaction (a set of queries) on this storage
			 *
			 * @param INativeTransaction $nativeTransaction
			 *
			 * @return IStream
			 */
			public function transaction(INativeTransaction $nativeTransaction): IStream;

			/**
			 * execute the given query against a storage; query should be translated into native query and
			 * executed by a native() method
			 *
			 * @param IQuery $query
			 *
			 * @return IStream
			 */
			public function execute(IQuery $query): IStream;

			/**
			 * directly execute native query
			 *
			 * @param INativeQuery $nativeQuery
			 *
			 * @return IStream
			 */
			public function query(INativeQuery $nativeQuery): IStream;
		}
