<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Collection\ICollection;
	use Edde\Query\IQuery;
	use Edde\Schema\ISchema;
	use Edde\Transaction\ITransaction;
	use Iterator;
	use stdClass;

	/**
	 * Low-level storage implementation with all supported query types explicitly typed.
	 */
	interface IStorage extends ITransaction {
		/**
		 * execute the given query and return native driver's result; this method does quite heavy
		 * job with translating input query into native query for this driver
		 *
		 * @param IQuery $query
		 *
		 * @return mixed
		 *
		 * @throws StorageException
		 */
		public function execute(IQuery $query);

		/**
		 * execute raw query which should return some data
		 *
		 * @param mixed $query
		 * @param array $params
		 *
		 * @return mixed
		 *
		 * @throws StorageException
		 */
		public function fetch($query, array $params = []);

		/**
		 * exec raw query without returning any data (create database, table, ...)
		 *
		 * @param mixed $query
		 * @param array $params
		 *
		 * @return mixed
		 *
		 * @throws StorageException
		 */
		public function exec($query, array $params = []);

		/**
		 * create new schema
		 *
		 * @param ISchema $schema
		 *
		 * @return IStorage
		 *
		 * @throws StorageException
		 */
		public function create(ISchema $schema): IStorage;

		/**
		 * optimized insert
		 *
		 * @param stdClass $source
		 * @param ISchema  $schema
		 *
		 * @return IStorage
		 *
		 * @throws StorageException
		 */
		public function insert(stdClass $source, ISchema $schema): IStorage;

		/**
		 * optimized update (by primary key)
		 *
		 * @param stdClass $source
		 * @param ISchema  $schema
		 *
		 * @return IStorage
		 */
		public function update(stdClass $source, ISchema $schema): IStorage;

		/**
		 * retrieve data specified by the given collection; kind of "SELECT FROM ..."
		 *
		 * @param ICollection $collection
		 *
		 * @return Iterator
		 */
		public function collection(ICollection $collection): Iterator;
	}
