<?php
	declare(strict_types = 1);

	namespace Edde\Api\Application;

	/**
	 * Lazy request dependency.
	 */
	trait LazyRequestTrait {
		/**
		 * @var IRequest
		 */
		protected $request;

		/**
		 * @param IRequest $request
		 */
		public function lazyRequest(IRequest $request) {
			$this->request = $request;
		}
	}
