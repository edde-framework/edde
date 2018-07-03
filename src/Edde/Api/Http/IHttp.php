<?php
	declare(strict_types=1);

	namespace Edde\Api\Http;

	use Edde\Api\Converter\IContent;

	/**
	 * "Abstract" interface holding common stuff between request and response.
	 */
	interface IHttp {
		/**
		 * @return IHeaderList
		 */
		public function getHeaderList(): IHeaderList;

		/**
		 * @return ICookieList|ICookie[]
		 */
		public function getCookieList(): ICookieList;

		/**
		 * shortcut to header list
		 *
		 * @param string $header
		 * @param string $value
		 *
		 * @return IHttp
		 */
		public function header(string $header, string $value): IHttp;

		/**
		 * set content of the request/response
		 *
		 * @param IContent|null $content
		 *
		 * @return IHttp
		 */
		public function setContent(IContent $content = null): IHttp;

		/**
		 * retrieve current content
		 *
		 * @return IContent|null
		 */
		public function getContent();
	}
