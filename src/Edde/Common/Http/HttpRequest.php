<?php
	declare(strict_types = 1);

	namespace Edde\Common\Http;

	use Edde\Api\Http\ICookieList;
	use Edde\Api\Http\IHeaderList;
	use Edde\Api\Http\IHttpRequest;
	use Edde\Api\Http\IHttpResponse;
	use Edde\Api\Http\IPostList;
	use Edde\Api\Http\IRequestUrl;
	use Edde\Api\Url\IUrl;
	use Edde\Common\Url\Url;

	class HttpRequest extends AbstractHttp implements IHttpRequest {
		/**
		 * @var IRequestUrl
		 */
		protected $requestUrl;
		/**
		 * @var IPostList
		 */
		protected $postList;
		/**
		 * @var string
		 */
		protected $method;
		/**
		 * @var string|null
		 */
		protected $remoteAddress;
		/**
		 * @var string|null
		 */
		protected $remoteHost;
		/**
		 * @var IHttpResponse
		 */
		protected $response;
		/**
		 * @var IUrl
		 */
		protected $referer;

		/**
		 * @param IPostList $postList
		 * @param IHeaderList $headerList
		 * @param ICookieList $cookieList
		 */
		public function __construct(IPostList $postList, IHeaderList $headerList, ICookieList $cookieList) {
			parent::__construct($headerList, $cookieList);
			$this->postList = $postList;
		}

		public function getRequestUrl(): IRequestUrl {
			return $this->requestUrl;
		}

		public function setRequestUrl(IRequestUrl $requestUrl): HttpRequest {
			$this->requestUrl = $requestUrl;
			return $this;
		}

		public function getPostList(): IPostList {
			return $this->postList;
		}

		public function setPostList(IPostList $postList) {
			$this->postList = $postList;
			return $this;
		}

		public function getMethod() {
			return $this->method;
		}

		public function setMethod(string $method): HttpRequest {
			$this->method = $method;
			return $this;
		}

		public function isMethod($method) {
			return strcasecmp($this->method, $method) === 0;
		}

		public function getRemoteAddress() {
			return $this->remoteAddress;
		}

		public function setRemoteAddress(string $remoteAddress): HttpRequest {
			$this->remoteAddress = $remoteAddress;
			return $this;
		}

		public function getRemoteHost() {
			if ($this->remoteHost === null && $this->remoteAddress !== null) {
				$this->remoteHost = gethostbyaddr($this->remoteAddress);
			}
			return $this->remoteHost;
		}

		public function setRemoteHost(string $remoteHost): HttpRequest {
			$this->remoteHost = $remoteHost;
			return $this;
		}

		public function getReferer() {
			if ($this->referer === null && $this->headerList->has('referer')) {
				$this->referer = new Url($this->headerList->get('referer'));
			}
			return $this->referer;
		}

		public function isSecured() {
			return $this->requestUrl->getScheme() === 'https';
		}

		public function isAjax() {
			return $this->headerList->get('X-Requested-With') === 'XMLHttpRequest';
		}
	}
