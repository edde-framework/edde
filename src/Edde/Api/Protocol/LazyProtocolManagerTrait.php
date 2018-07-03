<?php
	declare(strict_types=1);

	namespace Edde\Api\Protocol;

	trait LazyProtocolManagerTrait {
		/**
		 * @var IProtocolManager
		 */
		protected $protocolManager;

		/**
		 * @param IProtocolManager $protocolManager
		 */
		public function lazyProtocolManager(IProtocolManager $protocolManager) {
			$this->protocolManager = $protocolManager;
		}
	}
