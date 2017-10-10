<?php
	declare(strict_types=1);
	namespace Edde\Common\Http;

		use Edde\Api\Http\IContentType;
		use Edde\Api\Http\IHeaderList;
		use Edde\Api\Utils\Inject\HttpUtils;
		use Edde\Common\Collection\AbstractList;

		/**
		 * Simple header list implementation over an array.
		 */
		class HeaderList extends AbstractList implements IHeaderList {
			use HttpUtils;
			/**
			 * @var IContentType|null
			 */
			protected $contentType;
			/**
			 * @var string[]
			 */
			protected $acceptList;
			/**
			 * @var string[]
			 */
			protected $languageList;
			/**
			 * @var string[]
			 */
			protected $charsetList;

			/**
			 * @inheritdoc
			 */
			public function getContentType():?IContentType {
				if ($this->contentType === null && ($contentType = $this->get('Content-Type'))) {
					$this->contentType = new ContentType((string)$contentType);
				}
				return $this->contentType;
			}

			/**
			 * @inheritdoc
			 */
			public function getUserAgent(string $default = null) {
				return $this->get('User-Agent', $default);
			}

			/**
			 * @inheritdoc
			 */
			public function getAcceptList(): array {
				return $this->acceptList ?: $this->acceptList = $this->httpUtils->accept($this->get('Accept'));
			}

			/**
			 * @inheritdoc
			 */
			public function getAcceptLanguage(string $default): string {
				return $this->getAcceptLanguageList($default)[0];
			}

			/**
			 * @inheritdoc
			 */
			public function getAcceptLanguageList(string $default): array {
				return $this->languageList ?: $this->languageList = $this->httpUtils->language($this->get('Accept-Language'), $default);
			}

			/**
			 * @inheritdoc
			 */
			public function getAcceptCharset(string $default): string {
				return $this->getAcceptCharsetList($default)[0];
			}

			/**
			 * @inheritdoc
			 */
			public function getAcceptCharsetList(string $default): array {
				return $this->charsetList ?: $this->charsetList = $this->httpUtils->charset($this->get('Accept-Charset'), $default);
			}

			/**
			 * @inheritdoc
			 */
			public function headers(): array {
				$headers = [];
				foreach ($this->list as $header => $value) {
					$headers[] = $header . ': ' . $value;
				}
				return $headers;
			}

			/**
			 * @inheritdoc
			 */
			public function setupHeaderList(): IHeaderList {
				foreach ($this as $header => $value) {
					header("$header: $value");
				}
				return $this;
			}
		}
