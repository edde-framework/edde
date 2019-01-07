<?php
	declare(strict_types=1);
	namespace Edde\Job;

	use Edde\Configurable\IConfigurable;

	interface IJobExecutor extends IConfigurable {
		/**
		 * execute should (asynchronously) execute the given job
		 *
		 * @param string $job job uuid
		 *
		 * @return IJobExecutor
		 *
		 * @throws JobException
		 */
		public function execute(string $job): IJobExecutor;
	}
