<?php
	declare(strict_types = 1);

	namespace Edde\Api\Application;

	/**
	 * Lazy dependency on a response manager.
	 */
	trait LazyResponseManagerTrait {
		/**
		 * @var IResponseManager
		 */
		protected $responseManager;

		/**
		 * @param IResponseManager $responseManager
		 */
		public function lazyResponseManager(IResponseManager $responseManager) {
			$this->responseManager = $responseManager;
		}
	}
