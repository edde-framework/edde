<?php
	declare(strict_types=1);
	namespace Edde\Job;

	use Edde\Configurable\IConfigurable;

	interface IJobManager extends IConfigurable {
		/**
		 * executes infinite loop
		 *
		 * @return IJobManager
		 */
		public function run(): IJobManager;
	}
