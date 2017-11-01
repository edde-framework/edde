<?php
	namespace Edde\Api\Driver;

		use Edde\Api\Config\IConfigurable;
		use Edde\Api\Driver\Exception\DriverQueryException;
		use Edde\Api\Query\INativeQuery;
		use Edde\Api\Query\INativeTransaction;
		use Edde\Api\Storage\Exception\IntegrityException;
		use Edde\Api\Storage\IStream;

		/**
		 * General storage driver implementation; one storage could have more drivers to choose from.
		 */
		interface IDriver extends IConfigurable {
			/**
			 * translates and executes the given query
			 *
			 * @param INativeQuery $nativeQuery
			 *
			 * @return IStream
			 *
			 * @throws DriverQueryException
			 * @throws IntegrityException
			 */
			public function execute(INativeQuery $nativeQuery): IStream;

			/**
			 * return last result from the transaction
			 *
			 * @param INativeTransaction $nativeTransaction
			 *
			 * @return IStream
			 */
			public function transaction(INativeTransaction $nativeTransaction): IStream;

			/**
			 * start a transaction
			 *
			 * @return IDriver
			 */
			public function start(): IDriver;

			/**
			 * commit a transaction
			 *
			 * @return IDriver
			 */
			public function commit(): IDriver;

			/**
			 * rollback a transaction
			 *
			 * @return IDriver
			 */
			public function rollback(): IDriver;
		}
