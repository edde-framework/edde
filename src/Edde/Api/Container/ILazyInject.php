<?php
	declare(strict_types = 1);

	namespace Edde\Api\Container;

	/**
	 * Marker interface for classes supporting lazy inject.
	 */
	interface ILazyInject {
		/**
		 * marks property as lazy and provide callback which should fill a property; must work even for existing and private properties
		 *
		 * @param string $property
		 * @param callable $callback
		 *
		 * @return $this
		 */
		public function lazy(string $property, callable $callback);
	}
