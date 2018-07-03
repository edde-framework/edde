<?php
	declare(strict_types=1);

	namespace Edde\Api\Protocol;

	trait LazyProtocolServiceTrait {
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
