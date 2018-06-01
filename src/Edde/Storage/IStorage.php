<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Collection\EntityNotFoundException;
	use Edde\Collection\IEntity;
	use Edde\Filter\FilterException;
	use Edde\Query\IQuery;
	use Edde\Query\QueryException;
	use Edde\Schema\SchemaException;
	use Edde\Validator\ValidatorException;
	use Generator;

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
		 * @param array  $binds
		 *
		 * @return Generator|Row[]
		 *
		 * @throws StorageException
		 * @throws FilterException
		 * @throws QueryException
		 * @throws SchemaException
		 */
		public function query(IQuery $query, array $binds = []): Generator;

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
		 * @throws SchemaException
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
		 * @throws SchemaException
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
		 * save more entities in a transaction
		 *
		 * @param iterable $entities
		 *
		 * @return IStorage
		 *
		 * @throws StorageException
		 * @throws ValidatorException
		 * @throws FilterException
		 */
		public function saves(iterable $entities): IStorage;

		/**
		 * as the whole framework is using UUID as a common identifier, this method is
		 * optimized to get a data (one "table" at time) without messing with query builders
		 *
		 * @param string $schema
		 * @param string $id search by this value
		 *
		 * @return IEntity
		 *
		 * @throws StorageException
		 * @throws EntityNotFoundException
		 */
		public function load(string $schema, string $id): IEntity;

		/**
		 * delete given entity
		 *
		 * @param IEntity $entity
		 *
		 * @return IStorage
		 *
		 * @throws SchemaException
		 * @throws StorageException
		 */
		public function delete(IEntity $entity): IStorage;

		/**
		 * create a relation entity and make a relation between ($entity)-[$relation]->($target); relation
		 * MUST be saved explicitly as there could be mandatory attributes on it; $entity and $target got
		 * also saved in this method to ensure primary key presence
		 *
		 * @param IEntity $source
		 * @param IEntity $target
		 * @param string  $relation
		 *
		 * @return IEntity relation entity; it's possible to set up relation attributes and save entity
		 *
		 * @throws StorageException
		 * @throws ValidatorException
		 * @throws FilterException
		 * @throws SchemaException
		 */
		public function attach(IEntity $source, IEntity $target, string $relation): IEntity;

		/**
		 * remove all relations between given entities
		 *
		 * @param IEntity $entity
		 * @param IEntity $target
		 * @param string  $relation
		 *
		 * @return IStorage
		 *
		 * @throws StorageException
		 * @throws SchemaException
		 */
		public function unlink(IEntity $entity, IEntity $target, string $relation): IStorage;

		/**
		 * unlink all relations between given entities and make a new one; detach method is used for this
		 *
		 * @param IEntity $source
		 * @param IEntity $target
		 * @param string  $relation
		 *
		 * @return IEntity
		 *
		 * @throws StorageException
		 * @throws ValidatorException
		 * @throws FilterException
		 * @throws SchemaException
		 */
		public function link(IEntity $source, IEntity $target, string $relation): IEntity;

		/**
		 * get a count of items without order and limit (to get number of items for paging computation with applied where)
		 *
		 * @param IQuery $query
		 *
		 * @return int[]
		 *
		 * @throws StorageException
		 */
		public function count(IQuery $query): array;

		/**
		 * provides instance of compiler compatible with this storage
		 *
		 * @return ICompiler
		 */
		public function createCompiler(): ICompiler;
	}
