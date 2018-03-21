<?php
	declare(strict_types=1);
	namespace Edde\Inject\Connection;

	use Edde\Connection\IConnection;

	trait Connection {
		/**
		 * @var IConnection
		 */
		protected $connection;

		/**
		 * @param IConnection $connection
		 */
		public function lazyConnection(IConnection $connection): void {
			$this->connection = $connection;
		}
	}
