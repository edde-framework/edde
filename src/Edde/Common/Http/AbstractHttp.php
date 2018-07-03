<?php
	declare(strict_types=1);

	namespace Edde\Common\Http;

	use Edde\Api\Converter\IContent;
	use Edde\Api\Http\ICookieList;
	use Edde\Api\Http\IHeaderList;
	use Edde\Api\Http\IHttp;
	use Edde\Common\Object\Object;

	abstract class AbstractHttp extends Object implements IHttp {
		/**
		 * @var IHeaderList
		 */
		protected $headerList;
		/**
		 * @var ICookieList
		 */
		protected $cookieList;
		/**
		 * @var IContent
		 */
		protected $content;

		/**
		 * @param IHeaderList $headerList
		 * @param ICookieList $cookieList
		 */
		public function __construct(IHeaderList $headerList, ICookieList $cookieList) {
			$this->headerList = $headerList;
			$this->cookieList = $cookieList;
		}

		/**
		 * @inheritdoc
		 */
		public function getHeaderList(): IHeaderList {
			return $this->headerList;
		}

		/**
		 * @inheritdoc
		 */
		public function getCookieList(): ICookieList {
			return $this->cookieList;
		}

		/**
		 * @inheritdoc
		 */
		public function header(string $header, string $value): IHttp {
			$this->headerList->set($header, $value);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function setContent(IContent $content = null): IHttp {
			$this->content = $content;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getContent() {
			return $this->content;
		}
	}
