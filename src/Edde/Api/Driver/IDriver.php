<?php
	namespace Edde\Api\Driver;

		use Edde\Api\Config\IConfigurable;
		use Edde\Api\Driver\Exception\DriverQueryException;
		use Edde\Api\Query\INativeBatch;
		use Edde\Api\Query\INativeQuery;
		use Edde\Api\Storage\Exception\IntegrityException;
		use Edde\Api\Storage\IEntity;

		/**
		 * General storage driver implementation; one storage could have more drivers to choose from.
		 */
		interface IDriver extends IConfigurable {
			/**
			 * translates and executes the given query
			 *
			 * @param INativeQuery $nativeQuery
			 *
			 * @return mixed
			 *
			 * @throws DriverQueryException
			 * @throws IntegrityException
			 */
			public function execute(INativeQuery $nativeQuery);

			/**
			 * return last result from the batch
			 *
			 * @param INativeBatch $nativeBatch
			 *
			 * @return mixed
			 */
			public function batch(INativeBatch $nativeBatch);

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

			/**
			 * every driver could behave in different way when it comes to update:
			 * common database can do patch (partial update), some other engines
			 * could require whole data to be updated
			 *
			 * @param IEntity $entity
			 *
			 * @return array
			 */
			public function toArray(IEntity $entity): array;
		}
