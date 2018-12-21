<?php
	declare(strict_types=1);
	namespace Edde\Tick;

	use Edde\Configurable\IConfigurable;

	interface ITickService extends IConfigurable {
		/**
		 * process the given "tick"
		 *
		 * @return ITickService
		 */
		public function tick(): ITickService;
	}
