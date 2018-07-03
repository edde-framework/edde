<?php
	declare(strict_types=1);

	namespace Edde\Api\Database\Inject;

	use Edde\Api\Database\IDriver;

	trait Driver {
		/**
		 * @var IDriver
		 */
		protected $driver;

		public function lazyDriver(IDriver $driver) {
			$this->driver = $driver;
		}
	}
