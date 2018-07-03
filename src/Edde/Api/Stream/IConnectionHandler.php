<?php
	declare(strict_types=1);

	namespace Edde\Api\Stream;

	/**
	 * Physical implementation of server handler; this class should be able to handle
	 * all connection events.
	 */
	interface IConnectionHandler {
		/**
		 * @param IConnection $connection
		 *
		 * @return IConnectionHandler
		 */
		public function hello(IConnection $connection): IConnectionHandler;

		/**
		 * handle connection reads
		 *
		 * @param IConnection $connection
		 *
		 * @return IConnectionHandler
		 */
		public function read(IConnection $connection): IConnectionHandler;

		/**
		 * perform write to a connection
		 *
		 * @param string      $buffer
		 * @param IConnection $connection
		 *
		 * @return IConnectionHandler
		 */
		public function write(string $buffer, IConnection $connection): IConnectionHandler;
	}
