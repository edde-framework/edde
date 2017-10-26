<?php
	namespace Edde\Api\Driver;

		use Edde\Api\Config\IConfigurable;
		use Edde\Api\Driver\Exception\DriverQueryException;
		use Edde\Api\Driver\Exception\NativeQueryException;
		use Edde\Api\Query\INativeQuery;
		use Edde\Api\Query\IQuery;
		use Edde\Api\Storage\Exception\IntegrityException;

		/**
		 * General storage driver implementation; one storage could have more drivers to choose from.
		 */
		interface IDriver extends IConfigurable {
			/**
			 * executes native query on this engine
			 *
			 * @param INativeQuery $nativeQuery
			 *
			 * @return mixed
			 *
			 * @throws DriverQueryException
			 * @throws IntegrityException
			 */
			public function native(INativeQuery $nativeQuery);

			/**
			 * try to translate the given query to native query
			 *
			 * @param IQuery $query
			 *
			 * @return INativeQuery
			 *
			 * @throws NativeQueryException
			 */
			public function toNative(IQuery $query): INativeQuery;

			/**
			 * translates and executes the given query
			 *
			 * @param IQuery $query
			 *
			 * @return mixed
			 *
			 * @throws DriverQueryException
			 * @throws IntegrityException
			 */
			public function execute(IQuery $query);

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
			 * delimite input string
			 *
			 * @param string $delimite
			 *
			 * @return string
			 */
			public function delimite(string $delimite): string;

			/**
			 * quote input string
			 *
			 * @param string $delimite
			 *
			 * @return string
			 */
			public function quote(string $delimite): string;

			/**
			 * translate input type to engine internal type
			 *
			 * @param string $type
			 *
			 * @return string
			 */
			public function type(string $type): string;
		}
