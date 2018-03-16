<?php
	declare(strict_types=1);
	namespace Edde\Driver;

	use Edde\Api\Storage\Query\IQuery;
	use Edde\Config\IConfigurable;

	/**
	 * General driver for storage implementation; one storage could have more drivers to choose from.
	 */
	interface IDriver extends IConfigurable {
		/**
		 * execute the given query and return native driver's result; this method does quite heavy
		 * job with translating input query into native query for this driver
		 *
		 * @param IQuery $query
		 *
		 * @return mixed
		 *
		 * @throws DriverException
		 */
		public function execute(IQuery $query);

		/**
		 * execute raw query against a driver which should return some data
		 *
		 * @param mixed $query
		 * @param array $params
		 *
		 * @return mixed
		 *
		 * @throws DriverException
		 */
		public function fetch($query, array $params = []);

		/**
		 * exec raw query against a driver without returning any data (create database, table, ...)
		 *
		 * @param mixed $query
		 * @param array $params
		 *
		 * @return mixed
		 *
		 * @throws DriverException
		 */
		public function exec($query, array $params = []);

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
