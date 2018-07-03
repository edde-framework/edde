<?php
	declare(strict_types=1);

	namespace Edde\Api\Protocol\Inject;

	use Edde\Api\Protocol\IProtocolManager;

	trait ProtocolManager {
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
