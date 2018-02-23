<?php
	declare(strict_types=1);
	namespace Edde\Api\Http;

	/**
	 * Helper implementation of a http request header (GET /foo/bar HTTP/1.1)
	 */
	interface IRequestHeader {
		/**
		 * return uppercase method name
		 *
		 * @return string
		 */
		public function getMethod(): string;

		/**
		 * return request path
		 *
		 * @return string
		 */
		public function getPath(): string;

		/**
		 * return http protocol version number (1.0/1.1/2/...)
		 *
		 * @return string
		 */
		public function getVersion(): string;

		/**
		 * return header data in an array
		 *
		 * @return array
		 */
		public function toArray(): array;

		public function __toString(): string;
	}
