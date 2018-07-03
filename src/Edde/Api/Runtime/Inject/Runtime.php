<?php
	declare(strict_types=1);

	namespace Edde\Api\Runtime\Inject;

	use Edde\Api\Runtime\IRuntime;

	/**
	 * LAzy runtime dependency.
	 */
	trait Runtime {
		/**
		 * @var IRuntime
		 */
		protected $runtime;

		/**
		 * @param IRuntime $runtime
		 */
		public function lazyRuntime(IRuntime $runtime) {
			$this->runtime = $runtime;
		}
	}
