<?php
	declare(strict_types = 1);

	namespace Edde\Common\Http;

	use Edde\Api\Http\IContentType;
	use Edde\Api\Http\IHeaderList;
	use Edde\Common\Collection\AbstractList;

	/**
	 * Simple header list implementation over an array.
	 */
	class HeaderList extends AbstractList implements IHeaderList {
		/**
		 * @var IContentType
		 */
		protected $contentType;

		public function getContentType(): IContentType {
			if ($this->contentType === null) {
				$this->contentType = new ContentType($this->get('Content-Type', ''));
			}
			return $this->contentType;
		}

		public function getUserAgent(string $default = ''): string {
			return $this->get('User-Agent', $default);
		}

		public function getAccept(): string {
			return $this->getAcceptList()[0];
		}

		public function getAcceptList(): array {
			return HttpUtils::accept($this->get('Accept'));
		}

		public function getAcceptLanguage(string $default): string {
			return $this->getAccpetLanguageList($default)[0];
		}

		public function getAccpetLanguageList(string $default): array {
			return HttpUtils::language($this->get('Accept-Language'), $default);
		}

		public function getAcceptCharset(string $default): string {
			return $this->getAcceptCharsetList($default)[0];
		}

		public function getAcceptCharsetList(string $default): array {
			return HttpUtils::charset($this->get('Accept-Charset'), $default);
		}

		public function headers(): array {
			$headers = [];
			foreach ($this->list as $header => $value) {
				$headers[$header] = $header . ': ' . $value;
			}
			return $headers;
		}
	}
