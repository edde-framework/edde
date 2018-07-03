<?php
	declare(strict_types = 1);

	namespace Edde\Api\Acl;

	trait LazyAclTrait {
		/**
		 * @var IAcl
		 */
		protected $acl;

		public function lazyAcl(IAcl $acl) {
			$this->acl = $acl;
		}
	}
