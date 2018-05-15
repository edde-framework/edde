<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Query\IQuery;
	use Generator;
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
		 * @return Row[]|Generator
		 *
		 * @throws StorageException
		 */
		public function fetch($query, array $params = []): Generator;

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
		 * @return IRow[]|Generator
		 */
		public function execute(IQuery $query): Generator;

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
		 * @param string   $schema
		 * @param stdClass $source
		 *
		 * @return stdClass return data inserted into a storage (including defaults)
		 *
		 * @throws StorageException
		 */
		public function insert(string $schema, stdClass $source): stdClass;

		/**
		 * optimized update (by primary key)
		 *
		 * @param string   $schema
		 * @param stdClass $source
		 *
		 * @return IStorage
		 */
		public function update(string $schema, stdClass $source): IStorage;

		/**
		 * as the whole framework is using UUID as a common identifier, this method is
		 * optimized to get a data (one "table" at time) without messing with query builders
		 *
		 * internally method should use all unique properties to find desired model
		 *
		 * @param string $name
		 * @param string $id search by this value
		 *
		 * @return stdClass
		 */
		public function load(string $name, string $id): stdClass;
	}
