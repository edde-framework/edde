<?php
	declare(strict_types = 1);

	namespace Edde\Api\Http;

	use Edde\Api\Url\IUrl;

	/**
	 * Interface describing http request; it can has arbitrary usage, not only for wrapping of
	 * PHP's $_REQUEST/... variables.
	 */
	interface IHttpRequest extends IHttp {
		/**
		 * @return IRequestUrl
		 */
		public function getRequestUrl(): IRequestUrl;

		/**
		 * @param IPostList $postList
		 *
		 * @return $this
		 */
		public function setPostList(IPostList $postList);

		/**
		 * @return IPostList
		 */
		public function getPostList(): IPostList;

		/**
		 * @return string
		 */
		public function getMethod();

		/**
		 * @param string $method
		 *
		 * @return bool
		 */
		public function isMethod($method);

		/**
		 * @return null|string
		 */
		public function getRemoteAddress();

		/**
		 * @return null|string
		 */
		public function getRemoteHost();

		/**
		 * @return IUrl|null
		 */
		public function getReferer();

		/**
		 * @return bool
		 */
		public function isSecured();

		/**
		 * @return bool
		 */
		public function isAjax();
	}
