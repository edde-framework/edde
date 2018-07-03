<?php
	declare(strict_types=1);

	namespace Edde\Api\Database;

	trait LazyDriverTrait {
		/**
		 * @var IDriver
		 */
		protected $driver;

		public function lazyDriver(IDriver $driver) {
			$this->driver = $driver;
		}
	}
