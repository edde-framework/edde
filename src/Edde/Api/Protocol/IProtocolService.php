<?php
	declare(strict_types=1);

	namespace Edde\Api\Protocol;

	interface IProtocolService extends IProtocolHandler {
		/**
		 * @param IProtocolHandler $protocolHandler
		 *
		 * @return IProtocolService
		 */
		public function registerProtocolHandler(IProtocolHandler $protocolHandler): IProtocolService;

		/**
		 * just create a new packet
		 *
		 * @param IElement|null $reference
		 * @param string|null   $origin
		 *
		 * @return IElement
		 */
		public function createPacket(IElement $reference = null, string $origin = null): IElement;
	}
