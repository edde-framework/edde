<?php
	declare(strict_types=1);

	namespace Edde\Common\Collection;

	use ArrayIterator;
	use Edde\Api\Collection\IList;
	use Edde\Common\Object\Object;

	/**
	 * This list implementation is abstract because it should be not possible to use
	 * untyped lists accross an application.
	 */
	abstract class AbstractList extends Object implements IList {
		/**
		 * @var array
		 */
		protected $list = [];

		/**
		 * AbstractList constructor.
		 *
		 * @param array $list
		 */
		public function __construct(array $list = []) {
			$this->list = $list;
		}

		/**
		 * @inheritdoc
		 */
		public function isEmpty(): bool {
			return empty($this->list);
		}

		/**
		 * @inheritdoc
		 */
		public function put(array $array): IList {
			$this->list = $array;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function append(array $array): IList {
			$this->list = array_merge($this->list, $array);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function set(string $name, $value): IList {
			$this->list[$name] = $value;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function add(string $name, $value, $key = null): IList {
			if ($key) {
				$this->list[$name][$key] = $value;
				return $this;
			}
			$this->list[$name][] = $value;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function get(string $name, $default = null) {
			if ($this->has($name) === false) {
				return is_callable($default) ? call_user_func($default) : $default;
			}
			return $this->list[$name];
		}

		/**
		 * @inheritdoc
		 */
		public function has(string $name): bool {
			return isset($this->list[$name]) || array_key_exists($name, $this->list);
		}

		/**
		 * @inheritdoc
		 */
		public function array(): array {
			$array = [];
			foreach ($this->list as $k => $v) {
				$array[$k] = $v instanceof IList ? $v->array() : $v;
			}
			return $array;
		}

		/**
		 * @inheritdoc
		 */
		public function remove(string $name): IList {
			unset($this->list[$name]);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function clear(): IList {
			$this->list = [];
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getIterator() {
			return new ArrayIterator($this->list);
		}
	}
