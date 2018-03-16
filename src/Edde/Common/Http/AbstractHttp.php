<?php
	declare(strict_types=1);
	namespace Edde\Common\Http;

	use Edde\Content\IContent;
	use Edde\Http\IContentType;
	use Edde\Http\ICookies;
	use Edde\Http\IHeaders;
	use Edde\Http\IHttp;
	use Edde\Object;

	abstract class AbstractHttp extends Object implements \Edde\Http\IHttp {
		/**
		 * @var IHeaders
		 */
		protected $headers;
		/**
		 * @var ICookies
		 */
		protected $cookies;
		/**
		 * @var \Edde\Content\IContent|null
		 */
		protected $content;

		/**
		 * @param \Edde\Http\IHeaders $headers
		 * @param \Edde\Http\ICookies $cookies
		 */
		public function __construct(\Edde\Http\IHeaders $headers, ICookies $cookies) {
			$this->headers = $headers;
			$this->cookies = $cookies;
		}

		/**
		 * @inheritdoc
		 */
		public function getHeaders(): \Edde\Http\IHeaders {
			return $this->headers;
		}

		/**
		 * @inheritdoc
		 */
		public function getCookies(): ICookies {
			return $this->cookies;
		}

		/**
		 * @inheritdoc
		 */
		public function header(string $header, string $value): IHttp {
			$this->headers->add($header, $value);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function headers(array $headers): IHttp {
			$this->headers->put($headers);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function setContent(IContent $content = null): \Edde\Http\IHttp {
			$this->content = $content;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getContent(): ?IContent {
			return $this->content;
		}

		/**
		 * @inheritdoc
		 */
		public function setContentType(\Edde\Http\IContentType $contentType): \Edde\Http\IHttp {
			$this->headers->setContentType($contentType);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getContentType(): ?IContentType {
			return $this->headers->getContentType();
		}
	}
