<?php
	declare(strict_types=1);
	namespace Edde\Common\Http;

	use Edde\Api\Http\IContentType;
	use Edde\Api\Http\ICookie;
	use Edde\Api\Http\IHeaders;
	use Edde\Api\Http\IHttpUtils;
	use Edde\Api\Http\IRequestHeader;
	use Edde\Api\Http\IResponseHeader;
	use Edde\Common\Object\Object;
	use Edde\Common\Url\Url;
	use Edde\Exception\Http\HttpUtilsException;
	use Edde\Inject\Utils\StringUtils;

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
		public function contentType(string $contentType): IContentType {
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
			$mime = strtolower(trim(array_shift($type)));
			$parameterList = [];
			foreach ($type as $part) {
				$key = trim(substr($part, 0, $index = strpos($part, '=')));
				$value = trim(trim(substr($part, $index + 1)), '"');
				$parameterList[$key] = $value;
			}
			return new ContentType($mime, $parameterList);
		}

		/**
		 * @inheritdoc
		 */
		public function cookie(string $cookie): ICookie {
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
			$misc = $cookie['misc'];
			$cookie['expires'] = strtotime($cookie['expires'] ?? '1.1.1970');
			unset($cookie['misc']);
			return new Cookie($cookie['name'], $cookie['value'], $cookie['expires'], $cookie['path'] ?? '/', $cookie['domain'] ?? null, strpos($misc, 'secure') !== false, stripos($misc, 'httponly') !== false);
		}

		/**
		 * @inheritdoc
		 */
		public function parseHeaders(string $headers): IHeaders {
			return $this->headers(explode("\r\n", $headers));
		}

		/**
		 * @inheritdoc
		 */
		public function headers(array $headerList): IHeaders {
			$headers = new Headers();
			if (isset($headerList[0])) {
				try {
					$headers->add('http-request', $this->requestHeader($headerList[0]));
				} catch (HttpUtilsException $e) {
					try {
						$headers->add('http-response', $this->responseHeader($headerList[0]));
					} catch (HttpUtilsException $e) {
					}
				}
			}
			foreach ($headerList as $header) {
				if (($index = strpos($header, ':')) === false) {
					continue;
				}
				$name = substr($header, 0, $index);
				$value = trim(substr($header, $index + 1));
				switch (strtolower($name)) {
					case 'content-type':
						$value = $this->contentType($value);
						break;
					case 'host':
						$value = Url::create($value);
						break;
					case 'accept':
						$value = $this->accept($value);
						break;
					case 'accept-language':
						$value = $this->language($value);
						break;
				}
				$headers->add($name, $value);
			}
			return $headers;
		}

		/**
		 * @inheritdoc
		 */
		public function requestHeader(string $http): IRequestHeader {
			if ($match = $this->stringUtils->match($http, '~(?<method>[A-Z]+)\s+(?<path>.*?)\s+HTTP/(?<version>[0-9.]+)~', true)) {
				return new RequestHeader($match['method'], $match['path'], $match['version']);
			}
			throw new HttpUtilsException(sprintf('Cannot parse http request header [%s].', $http));
		}

		/**
		 * @inheritdoc
		 */
		public function responseHeader(string $http): IResponseHeader {
			if ($match = $this->stringUtils->match($http, '~^HTTP/(?<version>[0-9.]+)\s(?<status>\d+)(\s(?<message>.*))?$~', true)) {
				return new ResponseHeader($match['version'], (int)$match['status'], $match['message']);
			}
			throw new HttpUtilsException(sprintf('Cannot parse http response header [%s].', $http));
		}
	}
