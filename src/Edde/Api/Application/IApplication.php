<?php
	declare(strict_types = 1);

	namespace Edde\Api\Application;

	use Edde\Api\Deffered\IDeffered;
	use Edde\Api\Event\IEventBus;

	/**
	 * Single application implementation; per project should be exactly one instance (implementation) of this interface.
	 */
	interface IApplication extends IDeffered, IEventBus {
		/**
		 * execute main "loop" of application (process the given request)
		 *
		 * @return mixed
		 */
		public function run();
	}
