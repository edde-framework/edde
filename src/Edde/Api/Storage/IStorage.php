<?php
	namespace Edde\Api\Storage;

		use Edde\Api\Config\IConfigurable;
		use Edde\Api\Query\INativeQuery;
		use Edde\Api\Query\INativeTransaction;
		use Edde\Api\Query\IQuery;
		use Edde\Api\Schema\ISchema;
		use Edde\Api\Storage\Exception\DuplicateTableException;
		use Edde\Api\Storage\Exception\UnknownTableException;

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
			 * @return mixed
			 */
			public function transaction(INativeTransaction $nativeTransaction);

			/**
			 * execute the given query against a storage; query should be translated into native query and
			 * executed by a native() method
			 *
			 * @param IQuery $query
			 *
			 * @return mixed
			 */
			public function execute(IQuery $query);

			/**
			 * directly execute native query
			 *
			 * @param INativeQuery $nativeQuery
			 *
			 * @return mixed
			 */
			public function query(INativeQuery $nativeQuery);

			/**
			 * get a collection of entities (collection should use generator or iterator, never fetch to array)
			 *
			 * @param string      $schema
			 * @param IQuery|null $query
			 *
			 * @return ICollection|IEntity[]
			 * @throws UnknownTableException
			 */
			public function collection(string $schema, IQuery $query = null): ICollection;

			/**
			 * creates a new schema or throw an exception if already exists (or other error)
			 *
			 * @param ISchema $schema
			 *
			 * @return IStorage
			 * @throws DuplicateTableException
			 */
			public function createSchema(ISchema $schema): IStorage;
		}
