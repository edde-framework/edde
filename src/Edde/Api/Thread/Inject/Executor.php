<?php
	declare(strict_types=1);

	namespace Edde\Api\Thread\Inject;

	use Edde\Api\Thread\IExecutor;

	trait Executor {
		/**
		 * @var IExecutor
		 */
		protected $executor;

		/**
		 * @param IExecutor $executor
		 */
		public function lazyExecutor(IExecutor $executor) {
			$this->executor = $executor;
		}
	}
