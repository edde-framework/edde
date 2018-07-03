<?php
	declare(strict_types=1);

	namespace Edde\Api\Thread;

	use Edde\Api\Config\IConfigurable;

	/**
	 * Executor is responsible for thread spawning; it should be safe to execute it as many times
	 * as possible. When thread is killed (or softly terminated), executor should respawn it to
	 * process a new job.
	 */
	interface IExecutor extends IConfigurable {
		/**
		 * @param array|null $parameterList
		 *
		 * @return IExecutor
		 */
		public function execute(array $parameterList = null): IExecutor;
	}
