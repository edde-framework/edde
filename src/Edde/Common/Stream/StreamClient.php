<?php
	declare(strict_types=1);

	namespace Edde\Common\Stream;

	use Edde\Api\Stream\IConnector;
	use Edde\Api\Stream\IStreamClient;
	use Edde\Api\Stream\StreamClientException;

	class StreamClient extends AbstractConnector implements IStreamClient {
		public function connect(string $socket): IStreamClient {
			if (($stream = stream_socket_client($socket)) === false) {
				throw new StreamClientException('Cannot open client socket');
			}
			stream_set_blocking($stream, false);
			$this->connection = new Connection($this, $stream, stream_socket_get_name($stream, false));
			return $this;
		}

		public function read(callable $handler = null): string {
			return $this->connection->read($handler);
		}

		public function write(string $buffer): IStreamClient {
			$this->connection->write($buffer);
			return $this;
		}

		public function close(): IConnector {
			$this->connection->close();
			$this->connection = null;
			return $this;
		}
	}
