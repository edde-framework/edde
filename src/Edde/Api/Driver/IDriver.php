<?php
	declare(strict_types=1);
	namespace Edde\Api\Driver;

		use Edde\Api\Config\IConfigurable;
		use Edde\Api\Driver\Exception\DriverException;
		use Edde\Api\Query\IQuery;

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
			 * @throws DriverException
			 */
			public function execute(IQuery $query);

			/**
			 * execute native query on this driver without any additional processing
			 *
			 * @param mixed $query
			 * @param array $params
			 *
			 * @return mixed
			 */
			public function native($query, array $params = []);

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
