<?php
	declare(strict_types=1);
	namespace Edde\Inject\Driver;

	use Edde\Api\Driver\IDriver;

	trait Driver {
		/**
		 * @var IDriver
		 */
		protected $driver;

		/**
		 * @param IDriver $driver
		 */
		public function lazyDriver(IDriver $driver) {
			$this->driver = $driver;
		}
	}
