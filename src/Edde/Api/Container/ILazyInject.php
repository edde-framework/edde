<?php
	declare(strict_types=1);

	namespace Edde\Api\Container;

	interface ILazyInject {
		/**
		 * inject the given dependency to the property
		 *
		 * @param string $property
		 * @param mixed  $dependency
		 *
		 * @return $this
		 */
		public function inject(string $property, $dependency);

		/**
		 * register the given container dependency on the given property
		 *
		 * @param string     $property
		 * @param IContainer $container
		 * @param string     $dependency
		 * @param array      $parameterList
		 *
		 * @return $this
		 */
		public function lazy(string $property, IContainer $container, string $dependency, array $parameterList = []);
	}
