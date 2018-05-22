<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Collection\IEntity;
	use Edde\Filter\FilterException;
	use Edde\Query\IQuery;
	use Edde\Validator\ValidatorException;

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
		 * optimized insert; entity is updated with new values (for example generated uuid)
		 *
		 * @param IEntity $entity
		 *
		 * @return IStorage
		 *
		 * @throws StorageException
		 * @throws ValidatorException
		 * @throws FilterException
		 */
		public function insert(IEntity $entity): IStorage;

		/**
		 * optimized update (by primary key)
		 *
		 * @param IEntity $entity
		 *
		 * @return IStorage
		 *
		 * @throws StorageException
		 * @throws ValidatorException
		 * @throws FilterException
		 */
		public function update(IEntity $entity): IStorage;

		/**
		 * insert or update; if primary is not present, entity will be inserted directly
		 *
		 * @param IEntity $entity
		 *
		 * @return IStorage
		 *
		 * @throws StorageException
		 * @throws ValidatorException
		 * @throws FilterException
		 */
		public function save(IEntity $entity): IStorage;

		/**
		 * as the whole framework is using UUID as a common identifier, this method is
		 * optimized to get a data (one "table" at time) without messing with query builders
		 *
		 * @param string $schema
		 * @param string $id search by this value
		 *
		 * @return IEntity
		 */
		public function load(string $schema, string $id): IEntity;
	}
