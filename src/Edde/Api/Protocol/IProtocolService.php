<?php
	declare(strict_types=1);
	namespace Edde\Api\Protocol;

		interface IProtocolService extends IProtocolHandler {
			/**
			 * register the given protocol handler
			 *
			 * @param IProtocolHandler $protocolHandler
			 *
			 * @return IProtocolService
			 */
			public function registerProtocolHandler(IProtocolHandler $protocolHandler): IProtocolService;

			/**
			 * register set of protocol handlers
			 *
			 * @param IProtocolHandler[] $protocolHandlerList
			 *
			 * @return IProtocolService
			 */
			public function registerProtocolHandlerList(array $protocolHandlerList): IProtocolService;
		}
