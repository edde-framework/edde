<?php
	declare(strict_types=1);

	namespace Edde\Common\Stream;

	use Edde\Api\Stream\IConnection;
	use Edde\Api\Stream\IConnectionHandler;
	use Edde\Common\Object;

	abstract class AbstractConnectionHandler extends Object implements IConnectionHandler {
		public function hello(IConnection $connection): IConnectionHandler {
			return $this;
		}

		public function read(IConnection $connection): IConnectionHandler {
			/**
			 * empty lambda is here because it will throw away all the incoming data
			 */
			$connection->read(function ($data) {
			});
			return $this;
		}

		public function write(string $buffer, IConnection $connection): IConnectionHandler {
			return $this;
		}
	}
