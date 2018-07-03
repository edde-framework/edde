<?php
	declare(strict_types=1);

	namespace Edde\Api\Stream;

	interface IStreamClient extends IConnector {
		/**
		 * @param callable|null $handler
		 *
		 * @return string
		 */
		public function read(callable $handler = null): string;

		/**
		 * send data to the server
		 *
		 * @param string $buffer
		 *
		 * @return IStreamClient
		 */
		public function write(string $buffer): IStreamClient;
	}
