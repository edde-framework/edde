<?php
	declare(strict_types = 1);

	namespace Edde\Api\Crate;

	/**
	 * Crate loader is used for autoloader creation for crate support.
	 */
	interface ICrateLoader {
		/**
		 * invoke for autoloader callback
		 *
		 * @param string $class
		 *
		 * @return bool
		 */
		public function __invoke(string $class);
	}
