<?php
	declare(strict_types=1);

	namespace Edde\Common\Http;

	use Edde\Api\Http\IContentType;
	use Edde\Api\Http\IHeaderList;
	use Edde\Common\Collection\AbstractList;

	/**
	 * Simple header list implementation over an array.
	 */
	class HeaderList extends AbstractList implements IHeaderList {
		/**
		 * @var IHeaderList
		 */
		static protected $headerList;
		/**
		 * @var IContentType
		 */
		protected $contentType;
		/**
		 * @var array
		 */
		protected $acceptList;

		/**
		 * @inheritdoc
		 */
		public function getContentType() {
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
			return $this->acceptList ?: $this->acceptList = HttpUtils::accept($this->get('Accept'));
		}

		/**
		 * @inheritdoc
		 */
		public function getAcceptLanguage(string $default): string {
			return $this->getAccpetLanguageList($default)[0];
		}

		/**
		 * @inheritdoc
		 */
		public function getAccpetLanguageList(string $default): array {
			return HttpUtils::language($this->get('Accept-Language'), $default);
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
			return HttpUtils::charset($this->get('Accept-Charset'), $default);
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

		static public function createHeaderList(): IHeaderList {
			if (self::$headerList) {
				return self::$headerList;
			}
			$headers = [];
			$mysticList = [
				'CONTENT_TYPE'   => 'Content-Type',
				'CONTENT_LENGTH' => 'Content-Length',
				'CONTENT_MD5'    => 'Content-Md5',
			];
			/** @noinspection ForeachSourceInspection */
			foreach ($_SERVER as $key => $value) {
				if (empty($value)) {
					continue;
				}
				if (strpos($key, 'HTTP_') === 0) {
					$key = substr($key, 5);
					if (isset($mysticList[$key]) === false || isset($_SERVER[$key]) === false) {
						$key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))));
						$headers[$key] = $value;
					}
				} else if (isset($mysticList[$key])) {
					$headers[$mysticList[$key]] = $value;
				}
			}
			if (isset($headers['Authorization']) === false) {
				if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
					$headers['Authorization'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
				} else if (isset($_SERVER['PHP_AUTH_USER'])) {
					$password = $_SERVER['PHP_AUTH_PW'] ?? '';
					$headers['Authorization'] = 'Basic ' . base64_encode($_SERVER['PHP_AUTH_USER'] . ':' . $password);
				} else if (isset($_SERVER['PHP_AUTH_DIGEST'])) {
					$headers['Authorization'] = $_SERVER['PHP_AUTH_DIGEST'];
				}
			}
			return self::$headerList = new HeaderList($headers);
		}
	}
