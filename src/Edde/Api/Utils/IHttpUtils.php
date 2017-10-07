<?php
	declare(strict_types=1);
	namespace Edde\Api\Utils;

	use Edde\Api\Config\IConfigurable;

	interface IHttpUtils extends IConfigurable {
		/**
		 * parse accept header and return an ordered array with accept mime types
		 *
		 * @param string|null $accept
		 *
		 * @return array
		 */
		public function accept(string $accept = null): array;

		/**
		 * parse an input language string (Accept-Language header) and return langauge order
		 *
		 * @param string|null $language
		 * @param string      $default
		 *
		 * @return array
		 */
		public function language(string $language = null, string $default = 'en'): array;

		/**
		 * return ordered list of accepted charsets
		 *
		 * @param string|null $charset
		 * @param string      $default
		 *
		 * @return array
		 */
		public function charset(string $charset = null, $default = 'utf-8'): array;

		/**
		 * this method does some really dark magic, so if the output is wrong, try to look here and report a bug
		 *
		 * @param string $contentType
		 *
		 * @return \stdClass
		 */
		public function contentType(string $contentType): \stdClass;

		/**
		 * parse cookie and return simple cookie object
		 *
		 * @param string $cookie
		 *
		 * @return \stdClass
		 */
		public function cookie(string $cookie): \stdClass;

		/**
		 * return pure array of headers
		 *
		 * @param string $headers
		 * @param bool   $process
		 *
		 * @return array
		 */
		public function headerList(string $headers, bool $process = true): array;

		/**
		 * return array of headers with known headers processed to another structures (like parsed Content-Type)
		 *
		 * @param array $headerList
		 *
		 * @return array
		 */
		public function headers(array $headerList): array;

		/**
		 * parse http header (http request or http response)
		 *
		 * @param string $http
		 *
		 * @return \stdClass
		 */
		public function http(string $http): \stdClass;
	}
