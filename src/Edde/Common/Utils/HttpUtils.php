<?php
	declare(strict_types=1);
	namespace Edde\Common\Utils;

	use Edde\Api\Utils\IHttpUtils;
	use Edde\Api\Utils\Inject\StringUtils;
	use Edde\Common\Object\Object;

	/**
	 * Static set of helper functions around http protocol.
	 */
	class HttpUtils extends Object implements IHttpUtils {
		use StringUtils;

		/**
		 * @inheritdoc
		 */
		public function accept(string $accept = null): array {
			if ($accept === null) {
				return ['*/*'];
			}
			$accepts = [];
			foreach (explode(',', $accept) as $part) {
				if (($match = $this->stringUtils->match($part, '~\s*(?<mime>.+\/.+?)(?:\s*;\s*[qQ]\=(?<weight>[01](?:\.\d*)?))?\s*$~', true)) === null) {
					continue;
				}
				$weight = isset($match['weight']) ? (float)$match['weight'] : 1;
				if ($weight <= 0 || $weight > 1) {
					continue;
				}
				$accepts[] = [
					'mime'   => $match['mime'],
					'weight' => $weight,
				];
			}
			usort($accepts, function ($alpha, $beta) {
				if ($alpha['weight'] !== $beta['weight']) {
					return $alpha['weight'] < $beta['weight'];
				}
				$alphaMime = explode('/', $alpha['mime']);
				$betaMime = explode('/', $beta['mime']);
				if ($alphaMime[0] !== $betaMime[0]) {
					return 0;
				}
				if ($alphaMime[1] !== '*' && $betaMime[1] === '*') {
					return -1;
				}
				if ($alphaMime[1] === '*' && $betaMime[1] !== '*') {
					return 1;
				}
				if (strpos($alphaMime[1], ';') !== false) {
					return -1;
				}
				if (strpos($betaMime[1], ';') !== false) {
					return 1;
				}
				return 0;
			});
			$acceptList = [];
			foreach ($accepts as $value) {
				$acceptList[] = $value['mime'];
			}
			return $acceptList;
		}

		/**
		 * @inheritdoc
		 */
		public function language(string $language = null, string $default = 'en'): array {
			if ($language === null) {
				return [$default];
			}
			foreach (explode(',', $language) as $part) {
				if (($match = $this->stringUtils->match($part, '~\s*(?<lang>[^;]+)(?:\s*;\s*[qQ]\=(?<weight>[01](?:\.\d*)?))?\s*~', true)) === null) {
					continue;
				}
				$weight = isset($match['weight']) ? (float)$match['weight'] : 1;
				if ($weight < 0 || $weight > 1) {
					continue;
				}
				$langs[] = [
					'lang'   => $match['lang'],
					'weight' => $weight,
				];
			}
			usort($langs, function ($alpha, $beta) {
				return $alpha['weight'] < $beta['weight'];
			});
			$languageList = [];
			foreach ($langs as $value) {
				$languageList[] = $value['lang'];
			}
			return $languageList;
		}

		/**
		 * @inheritdoc
		 */
		public function charset(string $charset = null, $default = 'utf-8'): array {
			if ($charset === null) {
				return [$default];
			}
			foreach (explode(',', $charset) as $part) {
				if (($match = preg_match('~\s*(?<charset>[^;]+)(?:\s*;\s*[qQ]\=(?<weight>[01](?:\.\d*)?))?\s*~', $part)) === null) {
					continue;
				}
				$weight = isset($match['weight']) ? (float)$match['weight'] : 1;
				if ($weight < 0 || $weight > 1) {
					continue;
				}
				$charsets[] = [
					'charset' => $match['charset'],
					'weight'  => $weight,
				];
			}
			usort($charsets, function ($alpha, $beta) {
				return $alpha['weight'] < $beta['weight'];
			});
			$charsetList = [];
			foreach ($charsets as $value) {
				$charsetList[] = $value['charset'];
			}
			return $charsetList;
		}

		/**
		 * @inheritdoc
		 */
		public function contentType(string $contentType): \stdClass {
			/**
			 * this is fuckin' trick how to parse mime using native php's csv parser
			 *
			 * Content type header is separated by semicolons, so there is possibility to parse it like
			 * csv, because it also uses " as delimite character; this can give base output for further
			 * processing
			 */
			{
				/**
				 * open in-memory handler (we need few bytes, this is also overkill) and write content type there
				 */
				fwrite($handle = fopen('php://memory', 'rw'), $contentType);
				/**
				 * seek to the beginning of the memory block ("file")
				 */
				fseek($handle, 0, SEEK_SET);
				/**
				 * fgetcsv will do the job
				 */
				$type = fgetcsv($handle, null, ';', '"');
				/**
				 * clean up
				 */
				fclose($handle);
			}
			$stdClass = new \stdClass();
			$stdClass->mime = strtolower(trim(array_shift($type)));
			$stdClass->params = [];
			foreach ($type as $part) {
				$key = trim(substr($part, 0, $index = strpos($part, '=')));
				$value = trim(trim(substr($part, $index + 1)), '"');
				$stdClass->params[$key] = $value;
			}
			return $stdClass;
		}

		/**
		 * @inheritdoc
		 */
		public function cookie(string $cookie): \stdClass {
			$cookie = $this->stringUtils->match($cookie, '~(?<name>[^\s()<>@,;:\"/\\[\\]?={}]+)=(?<value>[^=;\s]+)\s*(?<misc>.*)?~', true);
			if (isset($cookie['misc'])) {
				if ($match = $this->stringUtils->match($cookie['misc'], '~path=(?<path>[a-z0-9/._-]+);?~i', true)) {
					$cookie['path'] = $match['path'];
				}
				if ($match = $this->stringUtils->match($cookie['misc'], '~domain=(?<domain>[a-z0-9._-]+);?~i', true)) {
					$cookie['domain'] = $match['domain'];
				}
				if ($match = $this->stringUtils->match($cookie['misc'], '~expires=(?<expires>[a-z0-9:\s,-]+);?~i', true)) {
					$cookie['expires'] = $match['expires'];
				}
			}
			$cookie['secure'] = strpos($cookie['misc'], 'secure') !== false;
			$cookie['httpOnly'] = stripos($cookie['misc'], 'httponly') !== false;
			unset($cookie['misc']);
			return (object)$cookie;
		}

		/**
		 * @inheritdoc
		 */
		public function headerList(string $headers, bool $process = true): array {
			$headers = explode("\r\n", $headers);
			$headerList = [];
			if ($this->stringUtils->match($headers[0], '~HTTP/[0-9.]+~')) {
				$headerList['http'] = array_shift($headers);
			}
			foreach ($headers as $header) {
				if (($index = strpos($header, ':')) === false) {
					continue;
				}
				$headerList[substr($header, 0, $index)] = trim(substr($header, $index + 1));
			}
			return $process ? self::headers($headerList) : $headerList;
		}

		/**
		 * @inheritdoc
		 */
		public function headers(array $headerList): array {
			$map = [
				'Content-Type'    => [
					$this,
					'contentType',
				],
				'http'            => [
					$this,
					'http',
				],
				'Accept'          => [
					$this,
					'accept',
				],
				'Accept-Language' => [
					$this,
					'language',
				],
			];
			foreach ($headerList as $name => &$header) {
				if (isset($map[$name]) === false) {
					continue;
				}
				$header = $map[$name]($header);
			}
			return $headerList;
		}

		/**
		 * @inheritdoc
		 */
		public function http(string $http): \stdClass {
			if ($match = $this->stringUtils->match($http, '~(?<method>[A-Z]+)\s+(?<path>.*?)\s+HTTP/(?<http>[0-9.]+)~', true)) {
				return (object)$match;
			}
			return (object)$this->stringUtils->match($http, '~^HTTP/(?<http>[0-9.]+)\s(?<status>\d+)(\s(?<message>.*))?$~', true);
		}
	}
