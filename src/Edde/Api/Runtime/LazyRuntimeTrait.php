<?php
	declare(strict_types=1);

	namespace Edde\Api\Runtime;

	/**
	 * LAzy runtime dependency.
	 */
	trait LazyRuntimeTrait {
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
