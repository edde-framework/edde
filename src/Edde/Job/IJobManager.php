<?php
	declare(strict_types=1);
	namespace Edde\Job;

	use Edde\Configurable\IConfigurable;

	interface IJobManager extends IConfigurable {
		/**
		 * executes infinite loop
		 *
		 * @return IJobManager
		 *
		 * @throws JobException
		 */
		public function run(): IJobManager;

		/**
		 * run one job if any
		 *
		 * @return IJobManager
		 *
		 * @throws JobException
		 */
		public function tick(): IJobManager;
	}
