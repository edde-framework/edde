<?php
	namespace Edde\Api\Database\Inject;

		use Edde\Api\Database\IDriver;

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
