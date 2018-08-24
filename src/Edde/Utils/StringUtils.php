<?php
	declare(strict_types=1);
	namespace Edde\Utils;

	use Edde\Edde;
	use Generator;
	use function is_array;

	class StringUtils extends Edde implements IStringUtils {
		const SEPARATOR_LIST = [
			'|',
			':',
			'.',
			'-',
			'_',
			'/',
			' ',
		];

		/** @inheritdoc */
		public function lower(string $string): string {
			return mb_strtolower($string, 'UTF-8');
		}

		/** @inheritdoc */
		public function substring(string $string, int $start, int $length = null): string {
			return mb_substr($string, $start, $length, 'UTF-8');
		}

		/** @inheritdoc */
		public function firstLower(string $string): string {
			return $this->lower($this->substring($string, 0, 1)) . $this->substring($string, 1);
		}

		/** @inheritdoc */
		public function capitalize(string $string): string {
			return mb_convert_case($string, MB_CASE_TITLE, 'UTF-8');
		}

		/** @inheritdoc */
		public function toCamelHump($input): string {
			return $this->firstLower($this->toCamelCase($input));
		}

		/** @inheritdoc */
		public function toCamelCase($input): string {
			return (string)str_replace('~', null, mb_convert_case(str_replace(self::SEPARATOR_LIST, '~', mb_strtolower(implode('~', is_array($input) ? $input : preg_split('~(?=[A-Z])~', $input, -1, PREG_SPLIT_NO_EMPTY)))), MB_CASE_TITLE, 'UTF-8'));
		}

		/** @inheritdoc */
		public function fromCamelCase(string $string, int $index = null): array {
			$camel = preg_split('~(?=[A-Z])~', $string, -1, PREG_SPLIT_NO_EMPTY);
			return $index !== null ? array_slice($camel, $index) : $camel;
		}

		/** @inheritdoc */
		public function recamel(string $string, string $glue = '-', int $index = 0): string {
			return mb_strtolower(implode($glue, $this->fromCamelCase($string, $index)));
		}

		/** @inheritdoc */
		public function match(string $string, string $pattern, bool $named = false, $trim = false): ?array {
			$match = null;
			if (($match = preg_match($pattern, $string, $match) ? $match : null) === null) {
				return is_array($trim) ? $trim : null;
			}
			if ($named && is_array($match)) {
				foreach ($match as $k => $v) {
					if (is_int($k) || ((is_array($trim) || $trim) && empty($v))) {
						unset($match[$k]);
					}
				}
			}
			if (is_array($trim)) {
				$match = array_merge($trim, $match);
			}
			return $match;
		}

		/** @inheritdoc */
		public function matchAll(string $string, string $pattern, bool $named = false, $trim = false): array {
			$match = null;
			if (($match = preg_match_all($pattern, $string, $match) ? $match : null) === null) {
				return is_array($trim) ? $trim : [];
			}
			if ($named) {
				/** @noinspection ForeachSourceInspection */
				foreach ($match as $k => $v) {
					if (is_int($k) || ((is_array($trim) || $trim) && empty($v))) {
						unset($match[$k]);
					}
				}
			}
			if (is_array($trim)) {
				$match = array_merge($trim, $match);
			}
			return $match;
		}

		/** @inheritdoc */
		public function extract(string $source, string $separator = '\\', int $index = -1): string {
			$sourceList = explode($separator, $source);
			return isset($sourceList[$index = ($index < 0 ? count($sourceList) + $index : $index)]) ? (string)$sourceList[$index] : '';
		}

		/** @inheritdoc */
		public function normalize(string $string): string {
			$string = $this->normalizeNewLines($string);
			$string = preg_replace('~[\x00-\x08\x0B-\x1F\x7F-\x9F]+~u', '', $string);
			$string = preg_replace('~[\t ]+$~m', '', $string);
			$string = trim($string, "\n");
			return $string;
		}

		/** @inheritdoc */
		public function normalizeNewLines(string $string): string {
			return (string)str_replace([
				"\r\n",
				"\r",
			], "\n", $string);
		}

		/** @inheritdoc */
		public function createIterator(string $string): Generator {
			$length = mb_strlen($string = $this->normalizeNewLines($string));
			while ($length) {
				yield mb_substr($string, 0, 1);
				$length = mb_strlen($string = mb_substr($string, 1, $length));
			}
		}

		/** @inheritdoc */
		public function className(string $string): string {
			return (string)str_replace(
				'°',
				'\\',
				$this->toCamelCase(
					str_replace(
						['.', '-'],
						['°', '~'],
						$string
					)
				)
			);
		}
	}
