<?php
	declare(strict_types=1);
	namespace Edde\Common\Utils;

		use Edde\Api\Utils\IStringUtils;
		use Edde\Common\Object\Object;

		class StringUtils extends Object implements IStringUtils {
			const SEPARATOR_LIST = [
				'|',
				':',
				'.',
				'-',
				'_',
				'/',
				' ',
			];

			/**
			 * @inheritdoc
			 */
			public function lower(string $string) : string {
				return mb_strtolower($string, 'UTF-8');
			}

			/**
			 * @inheritdoc
			 */
			public function substring(string $string, int $start, int $length = null) : string {
				return mb_substr($string, $start, $length, 'UTF-8');
			}

			/**
			 * @inheritdoc
			 */
			public function firstLower(string $string) : string {
				return $this->lower($this->substring($string, 0, 1)) . $this->substring($string, 1);
			}

			/**
			 * @inheritdoc
			 */
			public function capitalize(string $string) : string {
				return mb_convert_case($string, MB_CASE_TITLE, 'UTF-8');
			}

			/**
			 * @inheritdoc
			 */
			public function toCamelHump($input) : string {
				return $this->firstLower($this->toCamelCase($input));
			}

			/**
			 * @inheritdoc
			 */
			public function toCamelCase($input) : string {
				return str_replace('~', null, mb_convert_case(str_replace(self::SEPARATOR_LIST, '~', mb_strtolower(implode('~', is_array($input) ? $input : preg_split('~(?=[A-Z])~', $input, -1, PREG_SPLIT_NO_EMPTY)))), MB_CASE_TITLE, 'UTF-8'));
			}

			/**
			 * @inheritdoc
			 */
			public function match(string $string, string $pattern, bool $named = false, bool $trim = false) {
				$match = null;
				if (($match = preg_match($pattern, $string, $match) ? $match : null) === null) {
					return null;
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
		}
