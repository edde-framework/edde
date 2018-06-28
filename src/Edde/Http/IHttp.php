<?php
	declare(strict_types=1);
	namespace Edde\Http;

	use Edde\Content\IContent;

	/**
	 * "Abstract" interface holding common stuff between request and response.
	 */
	interface IHttp {
		/**
		 * @return IHeaders
		 */
		public function getHeaders(): IHeaders;

		/**
		 * shortcut to header list; this will add a new header (not replace)
		 *
		 * @param string $header
		 * @param string $value
		 *
		 * @return $this
		 */
		public function header(string $header, string $value): IHttp;

		/**
		 * add an array of headers at once
		 *
		 * @param array $headers
		 *
		 * @return $this
		 */
		public function headers(array $headers): IHttp;

		/**
		 * set content of the request/response
		 *
		 * @param IContent|null $content
		 *
		 * @return $this
		 */
		public function setContent(IContent $content = null): IHttp;

		/**
		 * retrieve current content
		 *
		 * @return IContent|null
		 */
		public function getContent(): ?IContent;
	}
