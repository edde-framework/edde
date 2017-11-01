<?php
	declare(strict_types=1);
	namespace Edde\Api\Driver\Inject;

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
