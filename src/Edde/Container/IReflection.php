<?php
	declare(strict_types=1);
	namespace Edde\Container;

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
		public function getParams(): array;

		/**
		 * get list of injectable parameters
		 *
		 * @return IParameter[]
		 */
		public function getInjects(): array;

		/**
		 * get list of lazy parameters
		 *
		 * @return IParameter[]
		 */
		public function getLazies(): array;

		/**
		 * return list of configurator names
		 *
		 * @return string[]
		 */
		public function getConfigurators(): array;
	}
