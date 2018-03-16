<?php
	declare(strict_types=1);
	namespace Edde\Http;

	interface IResponseHeader {
		/**
		 * return http protocol version (1.0/1.1/2/...)
		 *
		 * @return string
		 */
		public function getVersion(): string;

		/**
		 * return http status code
		 *
		 * @return int
		 */
		public function getCode(): int;

		/**
		 * return status message
		 *
		 * @return string
		 */
		public function getMessage(): string;

		/**
		 * return an array with response header
		 *
		 * @return array
		 */
		public function toArray(): array;

		/**
		 * @return string
		 */
		public function __toString(): string;
	}
