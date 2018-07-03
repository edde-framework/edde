<?php
	declare(strict_types=1);

	namespace Edde\Api\Identity;

	/**
	 * Lazy indentity dependency.
	 */
	trait LazyAuthorizatorTrait {
		/**
		 * @var IAuthorizator
		 */
		protected $authorizator;

		/**
		 * @param IAuthorizator $authorizator
		 */
		public function lazyAuthorizator(IAuthorizator $authorizator) {
			$this->authorizator = $authorizator;
		}
	}
