<?php
	declare(strict_types = 1);

	namespace Edde\Api\Http;

	use Edde\Api\Collection\IList;

	/**
	 * Explicit interface for http header list; missing array access interface is intentional.
	 */
	interface IHeaderList extends IList {
		/**
		 * return content type from header
		 *
		 * @return IContentType
		 */
		public function getContentType(): IContentType;

		/**
		 * return user agent
		 *
		 * @param string $default
		 *
		 * @return string
		 */
		public function getUserAgent(string $default = ''): string;

		/**
		 * return prioritized accept mime
		 *
		 * @return string
		 */
		public function getAccept(): string;

		/**
		 * return an ordered array of accepted mime types
		 *
		 * @return array
		 */
		public function getAcceptList(): array;

		/**
		 * return propritized language from the current header set or default one
		 *
		 * @param string $default
		 *
		 * @return string
		 */
		public function getAcceptLanguage(string $default): string;

		/**
		 * return an ordered array of accept-lang
		 *
		 * @param string $default
		 *
		 * @return array
		 */
		public function getAccpetLanguageList(string $default): array;

		/**
		 * return preffered charset
		 *
		 * @param string $default
		 *
		 * @return string
		 */
		public function getAcceptCharset(string $default): string;

		/**
		 * return an odered list of accept charsets
		 *
		 * @param string $default
		 *
		 * @return array
		 */
		public function getAcceptCharsetList(string $default): array;

		/**
		 * return array of "compiled" headers
		 *
		 * @return array
		 */
		public function headers(): array;
	}
