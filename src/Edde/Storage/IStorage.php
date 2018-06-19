<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Hydrator\IHydrator;
	use Edde\Schema\SchemaException;
	use Edde\Transaction\ITransaction;
	use Edde\Transaction\TransactionException;
	use Generator;
	use Throwable;

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
		public function fetch(string $query, array $params = []);

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
		public function exec(string $query, array $params = []);

		/**
		 * hydrate output with the given hydrator
		 *
		 * @param string    $query
		 * @param IHydrator $hydrator
		 * @param array     $params
		 *
		 * @return Generator generator or proprietary hydrator value
		 *
		 * @throws StorageException
		 */
		public function hydrate(string $query, IHydrator $hydrator, array $params = []): Generator;

		/**
		 * hydrate a single value from the query
		 *
		 * @param string $query
		 * @param array  $params
		 *
		 * @return Generator|mixed
		 *
		 * @throws StorageException
		 */
		public function value(string $query, array $params = []): Generator;

		/**
		 * hydrate the given schema from a query
		 *
		 * @param string $name
		 * @param string $query
		 * @param array  $params
		 *
		 * @return Generator|array
		 *
		 * @throws StorageException
		 */
		public function schema(string $name, string $query, array $params = []): Generator;

		/**
		 * insert a new item into storage ($name is schema name)
		 *
		 * @param string         $name
		 * @param array          $insert
		 * @param IHydrator|null $hydrator
		 *
		 * @return array
		 *
		 * @throws StorageException
		 */
		public function insert(string $name, array $insert, IHydrator $hydrator = null): array;

		/**
		 * insert multiple entities in a transaction
		 *
		 * @param string         $name
		 * @param iterable       $inserts
		 * @param IHydrator|null $hydrator
		 *
		 * @return IStorage
		 *
		 * @throws StorageException
		 * @throws TransactionException
		 */
		public function inserts(string $name, iterable $inserts, IHydrator $hydrator = null): IStorage;

		/**
		 * @param string         $name
		 * @param array          $update
		 * @param IHydrator|null $hydrator
		 *
		 * @return array
		 *
		 * @throws StorageException
		 */
		public function update(string $name, array $update, IHydrator $hydrator = null): array;

		/**
		 * save the given item (upsert)
		 *
		 * @param string         $name
		 * @param array          $save
		 * @param IHydrator|null $hydrator
		 *
		 * @return array
		 *
		 * @throws StorageException
		 */
		public function save(string $name, array $save, IHydrator $hydrator = null): array;

		/**
		 * create a new relation
		 *
		 * @param array  $source   exactly one key => value; (<schema name> => <uuid>)
		 * @param array  $target   exactly one key => value; (<schema name> => <uuid>)
		 * @param string $relation name of relation schema
		 *
		 * @return array relation (not saved yet)
		 *
		 * @throws StorageException
		 * @throws SchemaException
		 */
		public function attach(array $source, array $target, string $relation): array;

		/**
		 * load exactly one item or throw an exception
		 *
		 * @param string $name
		 * @param string $uuid
		 *
		 * @return array
		 *
		 * @throws StorageException
		 * @throws UnknownUuidException
		 */
		public function load(string $name, string $uuid): array;

		/**
		 * @param string $string
		 *
		 * @return string
		 */
		public function delimit(string $string): string;

		/**
		 * translate input exception to concrete storage exception
		 *
		 * @param Throwable $throwable
		 *
		 * @return Throwable
		 */
		public function exception(Throwable $throwable): Throwable;
	}
