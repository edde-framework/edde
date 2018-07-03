<?php
	declare(strict_types = 1);

	namespace Edde\Api\Http;

	/**
	 * "Abstract" interface holding common stuff between request and repsonse.
	 */
	interface IHttp {
		/**
		 * @param IHeaderList $headerList
		 *
		 * @return $this
		 */
		public function setHeaderList(IHeaderList $headerList);

		/**
		 * @return IHeaderList
		 */
		public function getHeaderList(): IHeaderList;

		/**
		 * @param ICookieList $cookieList
		 *
		 * @return $this
		 */
		public function setCookieList(ICookieList $cookieList);

		/**
		 * @return ICookieList
		 */
		public function getCookieList(): ICookieList;

		/**
		 * shortcut to header list
		 *
		 * @param string $header
		 * @param string $value
		 *
		 * @return $this
		 */
		public function header(string $header, string $value);

		/**
		 * set a content type
		 *
		 * @param string $contentType
		 *
		 * @return $this
		 */
		public function setContentType(string $contentType);

		/**
		 * @param IBody|null $body
		 *
		 * @return $this
		 */
		public function setBody(IBody $body = null);

		/**
		 * @return IBody|null
		 */
		public function getBody();
	}
