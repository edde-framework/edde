<?php
	declare(strict_types=1);

	namespace Edde\Api\Config;

	/**
	 * Marker interface for classes supporting external configuration.
	 */
	interface IConfigurable {
		/**
		 * register configurator
		 *
		 * @param IConfigurator $configurator
		 *
		 * @return $this
		 */
		public function addConfigurator(IConfigurator $configurator);

		/**
		 * register set of config handlers
		 *
		 * @param IConfigurator[] $configuratorList
		 *
		 * @return $this
		 */
		public function setConfiguratorList(array $configuratorList);

		/**
		 * this method should be called after all dependencies are
		 * available; also there should NOT be any heavy computations, only
		 * lightweight simple stuff
		 *
		 * @return
		 */
		public function init();

		/**
		 * do any heavy computations; after this object is usualy not serializable
		 */
		public function setup();
	}
