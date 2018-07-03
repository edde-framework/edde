<?php
	declare(strict_types=1);

	namespace Edde\Common\Http;

	use Edde\Api\Http\ICookie;
	use Edde\Common\Object\Object;
	use Edde\Common\Strings\StringException;
	use Edde\Common\Strings\StringUtils;

	/**
	 * Static set of helper functions around http protocol.
	 */
	class HttpUtils extends Object {
		/**
		 * parse accept header and return an ordered array with accept mime types
		 *
		 * @param string $accept
		 *
		 * @return array
		 * @throws StringException
		 */
		static public function accept(string $accept = null): array {
			if ($accept === null) {
				return ['*/*'];
			}
			$accepts = [];
			foreach (explode(',', $accept) as $part) {
				$match = StringUtils::match($part, '~\s*(?<mime>.+\/.+?)(?:\s*;\s*[qQ]\=(?<weight>[01](?:\.\d*)?))?\s*$~', true);
				if ($match === null) {
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
		 * parse an input language string (Accept-Language header) and return langauge order
		 *
		 * @param string $language
		 * @param string $default
		 *
		 * @return array
		 * @throws StringException
		 */
		static public function language(string $language = null, string $default = 'en'): array {
			if ($language === null) {
				return [$default];
			}
			foreach (explode(',', $language) as $part) {
				$match = StringUtils::match($part, '~\s*(?<lang>[^;]+)(?:\s*;\s*[qQ]\=(?<weight>[01](?:\.\d*)?))?\s*~', true);
				if ($match === null) {
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

		static public function charset(string $charset = null, $default = 'utf-8'): array {
			if ($charset === null) {
				return [$default];
			}
			foreach (explode(',', $charset) as $part) {
				$match = StringUtils::match($part, '~\s*(?<charset>[^;]+)(?:\s*;\s*[qQ]\=(?<weight>[01](?:\.\d*)?))?\s*~', true);
				if ($match === null) {
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
		 * this method does some really dark magic, so if the output is wrong, try to look here and report a bug
		 *
		 * @param string $contentType
		 *
		 * @return \stdClass
		 */
		static public function contentType(string $contentType): \stdClass {
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
				/** @noinspection PhpVariableVariableInspection */
				$stdClass->params[$key] = $value;
			}
			return $stdClass;
		}

		/**
		 * parse cookie and return array of values
		 *
		 * @param string $cookie
		 *
		 * @return ICookie
		 * @throws StringException
		 */
		static public function cookie(string $cookie): ICookie {
			/** @noinspection CallableParameterUseCaseInTypeContextInspection */
			$cookie = StringUtils::match($cookie, '~(?<name>[^\s()<>@,;:\"/\\[\\]?={}]+)=(?<value>[^=;\s]+)\s*(?<misc>.*)?~', true);
			$path = '/';
			$domain = '';
			$expires = '';
			if (isset($cookie['misc'])) {
				if ($match = StringUtils::match($cookie['misc'], '~path=(?<path>[a-z0-9/._-]+);?~i', true, true)) {
					$path = $match['path'];
				}
				if ($match = StringUtils::match($cookie['misc'], '~domain=(?<domain>[a-z0-9._-]+);?~i', true, true)) {
					$domain = $match['domain'];
				}
				if ($match = StringUtils::match($cookie['misc'], '~expires=(?<expires>[a-z0-9:\s,-]+\s+GMT);?~i', true, true)) {
					$expires = $match['expires'];
				}
			}
			return new Cookie($cookie['name'], $cookie['value'], $expires, $path, $domain, stripos($cookie['misc'], 'secure') !== false, stripos($cookie['misc'], 'httponly') !== false);
		}

		static public function headerList(string $headers, bool $process = true) {
			/** @noinspection CallableParameterUseCaseInTypeContextInspection */
			$headers = explode("\r\n", $headers);
			$headerList = [];
			if (stripos($headers[0], 'http') === 0) {
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

		static public function headers(array $headerList) {
			static $map = [
				'Content-Type' => [
					self::class,
					'contentType',
				],
				'http'         => [
					self::class,
					'http',
				],
			];
			foreach ($headerList as $name => &$header) {
				if (isset($map[$name]) === false) {
					continue;
				}
				$header = call_user_func($map[$name], $header);
			}
			return $headerList;
		}

		static public function http(string $http) {
			return (object)StringUtils::match($http, '~^HTTP/(?<version>\d+(\.\d+)?)\s(?<status>\d+)(\s(?<message>.*))?$~', true);
		}
	}
