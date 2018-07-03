<?php
	declare(strict_types=1);

	namespace Edde\Common\Stream;

	use Edde\Api\Stream\IConnection;
	use Edde\Api\Stream\IConnector;
	use Edde\Common\Object;

	abstract class AbstractConnector extends Object implements IConnector {
		/**
		 * @var IConnection
		 */
		protected $connection;

		/**
		 * @inheritdoc
		 */
		public function getConnection(): IConnection {
			return $this->connection;
		}
	}
