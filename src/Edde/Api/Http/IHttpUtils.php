<?php
	declare(strict_types=1);
	namespace Edde\Api\Http;

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
			 * parse an input language string (Accept-Language header) and return language order
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
			 * @return IContentType
			 */
			public function contentType(string $contentType): IContentType;

			/**
			 * parse cookie and return simple cookie object
			 *
			 * @param string $cookie
			 *
			 * @return ICookie
			 */
			public function cookie(string $cookie): ICookie;

			/**
			 * process input headers as a header implementation getting all known structures as parsed helper classes
			 *
			 * @param string $headers
			 *
			 * @return IHeaders
			 */
			public function parseHeaders(string $headers): IHeaders;

			/**
			 * same as parse headers, but headers are already in an array
			 *
			 * @param array $headerList
			 *
			 * @return IHeaders
			 */
			public function headers(array $headerList): IHeaders;

			/**
			 * parse http request header
			 *
			 * @param string $http
			 *
			 * @return IRequestHeader
			 */
			public function requestHeader(string $http): IRequestHeader;

			/**
			 * parse http response header
			 *
			 * @param string $http
			 *
			 * @return IResponseHeader
			 */
			public function responseHeader(string $http): IResponseHeader;
		}
