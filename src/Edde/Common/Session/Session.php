<?php
	declare(strict_types=1);

	namespace Edde\Common\Session;

	use ArrayIterator;
	use Edde\Api\Collection\IList;
	use Edde\Api\Session\ISession;
	use Edde\Common\Object;

	/**
	 * Session section for simple session data manipulation.
	 */
	class Session extends Object implements ISession {
		/**
		 * @var string
		 */
		protected $namespace;
		/**
		 * @var string
		 */
		protected $name;

		/**
		 * Q: Why did the computer go to the dentist?
		 * A: Because it had Bluetooth.
		 *
		 * @param string $namespace
		 * @param string $name
		 */
		public function __construct(string $namespace, string $name) {
			$this->namespace = $namespace;
			$this->name = $name;
		}

		/**
		 * @inheritdoc
		 */
		public function isEmpty(): bool {
			return empty($_SESSION[$this->namespace][$this->name]);
		}

		/**
		 * @inheritdoc
		 */
		public function put(array $array): IList {
			$_SESSION[$this->namespace][$this->name] = $array;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function set(string $name, $value): IList {
			$_SESSION[$this->namespace][$this->name][$name] = $value;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function add(string $name, $value, $key = null): IList {
			if ($key) {
				$_SESSION[$this->namespace][$this->name][$name][$key] = $value;
				return $this;
			}
			$_SESSION[$this->namespace][$this->name][$name][] = $value;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function has(string $name): bool {
			return isset($_SESSION[$this->namespace][$this->name][$name]);
		}

		/**
		 * @inheritdoc
		 */
		public function get(string $name, $default = null) {
			return $_SESSION[$this->namespace][$this->name][$name] ?? $default;
		}

		/**
		 * @inheritdoc
		 */
		public function array(): array {
			return $_SESSION[$this->namespace][$this->name];
		}

		/**
		 * @inheritdoc
		 */
		public function remove(string $name): IList {
			unset($_SESSION[$this->namespace][$this->name][$name]);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function clear(): IList {
			unset($_SESSION[$this->namespace][$this->name]);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getIterator() {
			return new ArrayIterator($_SESSION[$this->namespace][$this->name]);
		}
	}
