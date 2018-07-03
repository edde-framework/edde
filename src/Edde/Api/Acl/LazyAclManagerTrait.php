<?php
	declare(strict_types=1);

	namespace Edde\Api\Acl;

	trait LazyAclManagerTrait {
		/**
		 * @var IAclManager
		 */
		protected $aclManager;

		/**
		 * @param IAclManager $aclManager
		 */
		public function lazyAclManager(IAclManager $aclManager) {
			$this->aclManager = $aclManager;
		}
	}
