<?php
	declare(strict_types=1);
	namespace Edde\Connection;

	use Edde\Config\IConfigurable;
	use Edde\Query\IQuery;
	use Throwable;

	/**
	 * General driver for storage implementation; one storage could have more drivers to choose from.
	 */
	interface IConnection extends IConfigurable {
		/**
		 * execute the given query and return native driver's result; this method does quite heavy
		 * job with translating input query into native query for this driver
		 *
		 * @param IQuery $query
		 *
		 * @return mixed
		 *
		 * @throws ConnectionException
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
		 * @throws ConnectionException
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
		 * @throws ConnectionException
		 */
		public function exec($query, array $params = []);

		/**
		 * start a transaction
		 *
		 * @return IConnection
		 */
		public function start(): IConnection;

		/**
		 * commit a transaction
		 *
		 * @return IConnection
		 *
		 * @throws ConnectionException
		 */
		public function commit(): IConnection;

		/**
		 * rollback a transaction
		 *
		 * @return IConnection
		 */
		public function rollback(): IConnection;

		/**
		 * run a transaction and return result of callback
		 *
		 * @param callable $callback
		 *
		 * @return mixed
		 *
		 * @throws Throwable
		 */
		public function transaction(callable $callback);
	}
