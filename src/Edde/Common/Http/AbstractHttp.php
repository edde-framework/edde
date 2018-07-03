<?php
	declare(strict_types = 1);

	namespace Edde\Common\Http;

	use Edde\Api\Http\IBody;
	use Edde\Api\Http\ICookie;
	use Edde\Api\Http\ICookieList;
	use Edde\Api\Http\IHeaderList;
	use Edde\Api\Http\IHttp;
	use Edde\Common\AbstractObject;

	abstract class AbstractHttp extends AbstractObject implements IHttp {
		/**
		 * @var IHeaderList
		 */
		protected $headerList;
		/**
		 * @var ICookieList
		 */
		protected $cookieList;
		/**
		 * @var IBody
		 */
		protected $body;

		/**
		 * @param IHeaderList $headerList
		 * @param ICookieList $cookieList
		 */
		public function __construct(IHeaderList $headerList, ICookieList $cookieList) {
			$this->headerList = $headerList;
			$this->cookieList = $cookieList;
		}

		public function getHeaderList(): IHeaderList {
			return $this->headerList;
		}

		public function setHeaderList(IHeaderList $headerList) {
			$this->headerList = $headerList;
			return $this;
		}

		/**
		 * @return ICookieList|ICookie[]
		 */
		public function getCookieList(): ICookieList {
			return $this->cookieList;
		}

		public function setCookieList(ICookieList $cookieList) {
			$this->cookieList = $cookieList;
			return $this;
		}

		public function header(string $header, string $value) {
			$this->headerList->set($header, $value);
			return $this;
		}

		public function setContentType(string $contentType) {
			$this->headerList->set('Content-Type', $contentType);
			return $this;
		}

		public function getBody() {
			return $this->body;
		}

		public function setBody(IBody $body = null) {
			$this->body = $body;
			return $this;
		}
	}
