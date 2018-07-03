<?php
	declare(strict_types=1);

	namespace Edde\Api\Container;

	use Edde\Api\Reflection\IReflectionParameter;

	/**
	 * Describes dependency from point of view of object (or closure); so dependency is "me".
	 */
	interface IDependency {
		/**
		 * get list of mandatory parameters
		 *
		 * @return IReflectionParameter[]
		 */
		public function getParameterList(): array;

		/**
		 * get list of injectable parameters
		 *
		 * @return IReflectionParameter[]
		 */
		public function getInjectList(): array;

		/**
		 * get list of lazy parameters
		 *
		 * @return IReflectionParameter[]
		 */
		public function getLazyList(): array;

		/**
		 * return list of configurator names
		 *
		 * @return string[]
		 */
		public function getConfiguratorList(): array;
	}
