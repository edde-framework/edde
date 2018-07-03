<?php
	declare(strict_types=1);

	namespace Edde\Api\Protocol\Inject;

	use Edde\Api\Protocol\IProtocolService;

	trait ProtocolService {
		/**
		 * @var IProtocolService
		 */
		protected $protocolService;

		/**
		 * @param IProtocolService $protocolService
		 */
		public function lazyProtocolService(IProtocolService $protocolService) {
			$this->protocolService = $protocolService;
		}
	}
