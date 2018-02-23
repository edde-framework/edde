<?php
	declare(strict_types=1);
	namespace Edde\Api\Container;

	/**
	 * Container dependency reflection: holds mandatory parameters description, inject
	 * method reflection, lazy method reflection and names of configurators.
	 */
	interface IReflection {
		/**
		 * get list of mandatory parameters
		 *
		 * @return IParameter[]
		 */
		public function getParameterList(): array;

		/**
		 * get list of injectable parameters
		 *
		 * @return IParameter[]
		 */
		public function getInjectList(): array;

		/**
		 * get list of lazy parameters
		 *
		 * @return IParameter[]
		 */
		public function getLazyList(): array;

		/**
		 * return list of configurator names
		 *
		 * @return string[]
		 */
		public function getConfiguratorList(): array;
	}
