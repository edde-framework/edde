<?php
	declare(strict_types = 1);

	namespace Edde\Api\Identity;

	/**
	 * Lazy indentity manager dependency.
	 */
	trait LazyIdentityManagerTrait {
		/**
		 * @var IIdentityManager
		 */
		protected $identityManager;

		/**
		 * @param IIdentityManager $identityManager
		 */
		public function lazyIdentityManager(IIdentityManager $identityManager) {
			$this->identityManager = $identityManager;
		}
	}
