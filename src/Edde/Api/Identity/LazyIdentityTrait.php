<?php
	declare(strict_types=1);

	namespace Edde\Api\Identity;

	/**
	 * Lazy indentity dependency.
	 */
	trait LazyIdentityTrait {
		/**
		 * @var IIdentity
		 */
		protected $identity;

		/**
		 * @param IIdentity $identity
		 */
		public function lazyIdentity(IIdentity $identity) {
			$this->identity = $identity;
		}
	}
