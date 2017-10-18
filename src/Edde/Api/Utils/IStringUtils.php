<?php
	declare(strict_types=1);
	namespace Edde\Api\Utils;

		use Edde\Api\Config\IConfigurable;

		/**
		 * String utils interface.
		 */
		interface IStringUtils extends IConfigurable {
			/**
			 * make the string lowercase
			 *
			 * @param string $string
			 *
			 * @return string
			 */
			public function lower(string $string): string;

			/**
			 * return a substring from the given string
			 *
			 * @param string   $string
			 * @param int      $start
			 * @param int|null $length
			 *
			 * @return string
			 */
			public function substring(string $string, int $start, int $length = null): string;

			/**
			 * convert first character to lower case
			 *
			 * @param string $string
			 *
			 * @return string
			 */
			public function firstLower(string $string): string;

			/**
			 * try to capitalize input string
			 *
			 * @param string $string
			 *
			 * @return string
			 */
			public function capitalize(string $string): string;

			/**
			 * convert input string to camelHumpCase
			 *
			 * @param array|string $input
			 *
			 * @return string
			 */
			public function toCamelHump($input): string;

			/**
			 * return the given input as CamelCase
			 *
			 * @param string|array $input
			 *
			 * @return string
			 */
			public function toCamelCase($input): string;

			/**
			 * preg_match on steroids
			 *
			 * @param string $string
			 * @param string $pattern
			 * @param bool   $named
			 * @param bool   $trim
			 *
			 * @return array|null
			 */
			public function match(string $string, string $pattern, bool $named = false, bool $trim = false): ?array;

			/**
			 * @param string $string
			 * @param string $pattern
			 * @param bool   $named
			 * @param bool   $trim
			 *
			 * @return array
			 */
			public function matchAll(string $string, string $pattern, bool $named = false, $trim = false): array;

			/**
			 * extract particular string from another simply formatted string (for example class name from namespace, ...)
			 *
			 * @param string $source
			 * @param string $separator
			 * @param int    $index
			 *
			 * @return string
			 */
			public function extract(string $source, string $separator = '\\', int $index = -1): string;

			/**
			 * translate an inconsistent newlines to the standard "\n"
			 *
			 * @param string $string
			 *
			 * @return string
			 */
			public function normalizeNewLines(string $string): string;

			/**
			 * normalize input string, trim newlines, ...
			 *
			 * @param string $string
			 *
			 * @return string
			 */
			public function normalize(string $string): string;

			/**
			 * create an iterator over the given string
			 *
			 * @param string $string
			 *
			 * @return \Generator
			 */
			public function createIterator(string $string): \Generator;
		}
