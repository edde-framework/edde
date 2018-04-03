<?php
	declare(strict_types=1);
	namespace Edde\Container;

	/**
	 * This is a formal interface for classes supporting autowiring. The specification
	 * forces container to respect this interface, thus inject all dependencies based on
	 * all available inject methods.
	 */
	interface IAutowire {
		/**
		 * register the given container dependency on the given property; long name for this method is intentional
		 * as it's not intended for a common use
		 *
		 * @param string     $property
		 * @param IContainer $container
		 * @param string     $dependency
		 * @param array      $params
		 *
		 * @return $this
		 */
		public function registerAutowireDependency(string $property, IContainer $container, string $dependency, array $params = []);
	}
