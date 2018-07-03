<?php
	declare(strict_types = 1);

	namespace Edde\Common;

	use Edde\Api\EddeException;

	/**
	 * General php object protection trait; this should be used in every class.
	 */
	trait ObjectTrait {
		/**
		 * @var callable[]
		 */
		protected $objectPropertyList = [];

		/**
		 * alias to self::objectProperty()
		 *
		 * @param string $property
		 * @param callable $callback
		 *
		 * @return $this
		 */
		public function lazy(string $property, callable $callback) {
			return $this->objectProperty($property, $callback);
		}

		/**
		 * magical method for replacing defined property with a deffered magical method for later value loading
		 *
		 * @param string $property
		 * @param callable $callback
		 *
		 * @return $this
		 */
		public function objectProperty(string $property, callable $callback) {
			$this->objectPropertyList[$property] = $callback;
			/**
			 * this magic allows to remove a private property, ou yay!
			 */
			call_user_func(\Closure::bind(function (string $property) {
				/** @noinspection PhpVariableVariableInspection */
				unset($this->$property);
			}, $this, static::class), $property);
			return $this;
		}

		/**
		 * @param string $name
		 *
		 * @return mixed
		 * @throws EddeException
		 */
		public function __get(string $name) {
			if (isset($this->objectPropertyList[$name])) {
				/** @noinspection PhpVariableVariableInspection */
				return $this->$name = call_user_func($this->objectPropertyList[$name]);
			}
			throw new EddeException(sprintf('Reading from the undefined/private/protected property [%s::$%s].', static::class, $name));
		}

		/**
		 * @param string $name
		 * @param mixed $value
		 *
		 * @return $this
		 * @throws EddeException
		 */
		public function __set(string $name, $value) {
			if (isset($this->objectPropertyList[$name])) {
				/** @noinspection PhpVariableVariableInspection */
				$this->$name = $value;
				return $this;
			}
			throw new EddeException(sprintf('Writing to the undefined/private/protected property [%s::$%s].', static::class, $name));
		}

		/**
		 * @param string $name
		 *
		 * @return bool
		 * @throws EddeException
		 */
		public function __isset(string $name) {
			if (isset($this->objectPropertyList[$name])) {
				return true;
			}
			throw new EddeException(sprintf('Cannot check isset on undefined/private/protected property [%s::$%s].', static::class, $name));
		}
	}
