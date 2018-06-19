<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Hydrator\IHydrator;
	use Edde\Transaction\ITransaction;
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
