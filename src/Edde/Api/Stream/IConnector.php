<?php
	declare(strict_types=1);

	namespace Edde\Api\Stream;

	interface IConnector {
		/**
		 * @return IConnection
		 */
		public function getConnection(): IConnection;

		/**
		 * @return IConnector
		 */
		public function close(): IConnector;
	}
