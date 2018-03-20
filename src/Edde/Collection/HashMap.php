<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	use Edde\Object;
	use stdClass;

	/**
	 * This list implementation is abstract because it should be not possible to use
	 * untyped lists across an application.
	 */
	class HashMap extends Object implements IHashMap {
		/** @var array */
		protected $hashMap = [];

		public function __construct(stdClass $hashMap = null) {
			$this->hashMap = $hashMap ?? new stdClass();
		}

		/** @inheritdoc */
		public function isEmpty(): bool {
			return empty((array)$this->hashMap);
		}

		/** @inheritdoc */
		public function put(stdClass $object): IHashMap {
			$this->hashMap = clone $object;
			return $this;
		}

		/** @inheritdoc */
		public function merge(stdClass $object): IHashMap {
			$this->hashMap = (object)array_merge((array)$this->hashMap, (array)$object);
			return $this;
		}

		/** @inheritdoc */
		public function set(string $name, $value): IHashMap {
			$this->hashMap->$name = $value;
			return $this;
		}

		/** @inheritdoc */
		public function get(string $name, $default = null) {
			if ($this->has($name) === false) {
				return is_callable($default) ? call_user_func($default) : $default;
			}
			return $this->hashMap->$name;
		}

		/** @inheritdoc */
		public function has(string $name): bool {
			return isset($this->hashMap->$name);
		}

		/** @inheritdoc */
		public function toObject(): stdClass {
			$object = new stdClass();
			foreach ($this->hashMap as $k => $v) {
				$object->$k = $v instanceof IHashMap ? $v->toObject() : $v;
			}
			return $object;
		}

		/** @inheritdoc */
		public function remove(string $name): IHashMap {
			unset($this->hashMap->$name);
			return $this;
		}

		/** @inheritdoc */
		public function clear(): IHashMap {
			$this->hashMap = new stdClass();
			return $this;
		}

		/** @inheritdoc */
		public function getIterator() {
			yield from (array)$this->hashMap;
		}

		/** @inheritdoc */
		public function __clone() {
			parent::__clone();
			$this->hashMap = clone $this->hashMap;
		}
	}
