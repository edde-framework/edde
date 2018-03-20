<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	use IteratorAggregate;
	use stdClass;

	/**
	 * Simple list interface for array type checking (for a little bit more complex types than arrays).
	 */
	interface IHashMap extends IteratorAggregate {
		/**
		 * is the list empty?
		 *
		 * @return bool
		 */
		public function isEmpty(): bool;

		/**
		 * @param stdClass $object
		 *
		 * @return IHashMap
		 */
		public function put(stdClass $object): IHashMap;

		/**
		 * @param stdClass $object
		 *
		 * @return IHashMap
		 */
		public function merge(stdClass $object): IHashMap;

		/**
		 * @param string $name
		 * @param mixed  $value
		 *
		 * @return IHashMap
		 */
		public function set(string $name, $value): IHashMap;

		/**
		 * return true if the given name is set (present) even with null value
		 *
		 * @param string $name
		 *
		 * @return bool
		 */
		public function has(string $name): bool;

		/**
		 * @param string               $name
		 * @param string|callable|null $default
		 *
		 * @return mixed|null|IHashMap
		 */
		public function get(string $name, $default = null);

		/**
		 * @return stdClass
		 */
		public function toObject(): stdClass;

		/**
		 * @param string $name
		 *
		 * @return IHashMap
		 */
		public function remove(string $name): IHashMap;

		/**
		 * @return IHashMap
		 */
		public function clear(): IHashMap;
	}
