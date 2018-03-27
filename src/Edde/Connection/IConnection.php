<?php
	declare(strict_types=1);
	namespace Edde\Connection;

	use Edde\Query\IQuery;
	use Edde\Schema\ISchema;
	use Edde\Transaction\ITransaction;
	use stdClass;

	/**
	 * General driver for storage implementation; one storage could have more drivers to choose from.
	 */
	interface IConnection extends ITransaction {
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
		 * execute raw query which should return some data
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
		 * exec raw query without returning any data (create database, table, ...)
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
		 * create new schema
		 *
		 * @param ISchema $schema
		 *
		 * @return IConnection
		 *
		 * @throws ConnectionException
		 */
		public function create(ISchema $schema): IConnection;

		/**
		 * optimized insert
		 *
		 * @param stdClass $source
		 * @param ISchema  $schema
		 *
		 * @return IConnection
		 *
		 * @throws ConnectionException
		 */
		public function insert(stdClass $source, ISchema $schema): IConnection;

		/**
		 * optimized update (by primary key)
		 *
		 * @param stdClass $source
		 * @param ISchema  $schema
		 *
		 * @return IConnection
		 */
		public function update(stdClass $source, ISchema $schema): IConnection;
	}
