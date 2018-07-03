<?php
	declare(strict_types = 1);

	namespace Edde\Common\Collection;

	use ArrayIterator;
	use Edde\Api\Collection\IList;
	use Edde\Common\AbstractObject;

	/**
	 * This list implementation is abstract because it should be not possible to use
	 * untyped lists accross an application.
	 */
	abstract class AbstractList extends AbstractObject implements IList {
		/**
		 * @var string[]
		 */
		protected $list = [];

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
		public function set(string $name, $value): IList {
			$this->list[$name] = $value;
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
			return $this->list;
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
		public function getIterator() {
			return new ArrayIterator($this->list);
		}
	}
