<?php
	declare(strict_types=1);
	namespace Edde\Inject\Driver;

	use Edde\Driver\IDriver;

	trait Driver {
		/**
		 * @var \Edde\Driver\IDriver
		 */
		protected $driver;

		/**
		 * @param IDriver $driver
		 */
		public function lazyDriver(\Edde\Driver\IDriver $driver) {
			$this->driver = $driver;
		}
	}
