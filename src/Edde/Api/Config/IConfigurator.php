<?php
	declare(strict_types=1);

	namespace Edde\Api\Config;

	/**
	 * This interface is general way how to implement service configuration (or configuration of almost anything).
	 */
	interface IConfigurator {
		/**
		 * run config over the given instance
		 *
		 * @param mixed $instance
		 */
		public function configure($instance);
	}
