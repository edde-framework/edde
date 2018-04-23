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
		 * execute the given query; in fact, there could be more native queries than just one
		 * Query
		 *
		 * @param IQuery $query
		 *
		 * @return mixed
		 */
		public function execute(IQuery $query);

		/**
		 * create new schema (not all storages may support this, but exception should not be thrown)
		 *
		 * @param string $name schema name
		 *
		 * @return IStorage
		 *
		 * @throws StorageException
		 */
		public function create(string $name): IStorage;

		/**
		 * optimized insert
		 *
		 * @param stdClass $source
		 * @param string   $name
		 *
		 * @return stdClass return data inserted into a storage (including defaults)
		 *
		 * @throws StorageException
		 */
		public function insert(stdClass $source, string $name): stdClass;

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
		 * as the whole framework is using UUID as a common identifier, this method is
		 * optimized to get a data (one "table" at time) without messing with query builders
		 *
		 * internally method should use all unique properties to find desired model
		 *
		 * @param string $name
		 * @param string $key
		 *
		 * @return stdClass
		 */
		public function load(string $name, string $key): stdClass;

		/**
		 * retrieve data specified by the given collection; kind of "SELECT FROM ..."
		 *
		 * @param ICollection $collection
		 *
		 * @return Iterator
		 */
		public function collection(ICollection $collection): Iterator;
	}
