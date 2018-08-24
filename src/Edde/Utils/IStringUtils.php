<?php
	declare(strict_types=1);
	namespace Edde\Utils;

	use Generator;

	/**
	 * String utils interface.
	 */
	interface IStringUtils {
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
		 * split the given string by capital letters (e.g. FooBar will became [Foo, Bar])
		 *
		 * @param string $string
		 * @param int    $index
		 *
		 * @return array
		 */
		public function fromCamelCase(string $string, int $index = null): array;

		/**
		 * split given string by capital letters and glue them by the given glue - e.g. FooBar will became foo-bar
		 *
		 * @param string $string
		 * @param string $glue
		 * @param int    $index
		 *
		 * @return string
		 */
		public function recamel(string $string, string $glue = '-', int $index = 0): string;

		/**
		 * preg_match on steroids
		 *
		 * @param string     $string
		 * @param string     $pattern
		 * @param bool       $named
		 * @param bool|array $trim
		 *
		 * @return array|null
		 */
		public function match(string $string, string $pattern, bool $named = false, $trim = false): ?array;

		/**
		 * @param string     $string
		 * @param string     $pattern
		 * @param bool       $named
		 * @param bool|array $trim
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
		 * @return Generator
		 */
		public function createIterator(string $string): Generator;

		/**
		 * converts lowercase notation to PHP FQDN class name with namespace (if present), for
		 * example foo-bar.some-thing will be FooBar\\SomeThing
		 *
		 * @param string $string
		 *
		 * @return string
		 */
		public function className(string $string): string;
	}
