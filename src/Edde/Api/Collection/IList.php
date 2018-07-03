<?php
	declare(strict_types = 1);

	namespace Edde\Api\Collection;

	use IteratorAggregate;

	/**
	 * Simple list interface for array type checking (for a little bit more complex types than arrays).
	 */
	interface IList extends IteratorAggregate {
		/**
		 * is the list empty?
		 *
		 * @return bool
		 */
		public function isEmpty(): bool;

		/**
		 * @param array $array
		 *
		 * @return IList
		 */
		public function put(array $array): IList;

		/**
		 * @param string $name
		 * @param string $value
		 *
		 * @return IList
		 */
		public function set(string $name, $value): IList;

		/**
		 * return true if the given name is set (present) even with null value
		 *
		 * @param string $name
		 *
		 * @return bool
		 */
		public function has(string $name): bool;

		/**
		 * @param string $name
		 * @param string|callable|null $default
		 *
		 * @return mixed
		 */
		public function get(string $name, $default = null);

		/**
		 * @return array
		 */
		public function array(): array;

		/**
		 * @param string $name
		 *
		 * @return IList
		 */
		public function remove(string $name): IList;
	}
