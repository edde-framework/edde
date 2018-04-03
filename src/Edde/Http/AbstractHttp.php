<?php
	declare(strict_types=1);
	namespace Edde\Http;

	use Edde\Content\IContent;
	use Edde\Obj3ct;

	abstract class AbstractHttp extends Obj3ct implements IHttp {
		/** @var IHeaders */
		protected $headers;
		/** @var ICookies */
		protected $cookies;
		/** @var IContent|null */
		protected $content;

		/**
		 * @param IHeaders $headers
		 * @param ICookies $cookies
		 */
		public function __construct(IHeaders $headers, ICookies $cookies) {
			$this->headers = $headers;
			$this->cookies = $cookies;
		}

		/** @inheritdoc */
		public function getHeaders(): IHeaders {
			return $this->headers;
		}

		/** @inheritdoc */
		public function getCookies(): ICookies {
			return $this->cookies;
		}

		/** @inheritdoc */
		public function header(string $header, string $value): IHttp {
			$this->headers->add($header, $value);
			return $this;
		}

		/** @inheritdoc */
		public function headers(array $headers): IHttp {
			$this->headers->put($headers);
			return $this;
		}

		/** @inheritdoc */
		public function setContent(IContent $content = null): IHttp {
			$this->content = $content;
			return $this;
		}

		/** @inheritdoc */
		public function getContent(): ?IContent {
			return $this->content;
		}

		/** @inheritdoc */
		public function setContentType(IContentType $contentType): IHttp {
			$this->headers->setContentType($contentType);
			return $this;
		}

		/** @inheritdoc */
		public function getContentType(): ?IContentType {
			return $this->headers->getContentType();
		}
	}
