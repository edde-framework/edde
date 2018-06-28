<?php
	declare(strict_types=1);
	namespace Edde\Http;

	use Edde\Content\IContent;
	use Edde\Edde;

	abstract class AbstractHttp extends Edde implements IHttp {
		/** @var IHeaders */
		protected $headers;
		/** @var IContent|null */
		protected $content;

		/**
		 * @param IHeaders $headers
		 */
		public function __construct(IHeaders $headers) {
			$this->headers = $headers;
		}

		/** @inheritdoc */
		public function getHeaders(): IHeaders {
			return $this->headers;
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
