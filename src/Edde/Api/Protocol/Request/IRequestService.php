<?php
	declare(strict_types=1);

	namespace Edde\Api\Protocol\Request;

	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\IProtocolHandler;

	interface IRequestService extends IProtocolHandler {
		/**
		 * @param IRequestHandler $requestHandler
		 *
		 * @return IRequestService
		 */
		public function registerRequestHandler(IRequestHandler $requestHandler): IRequestService;

		/**
		 * return list of current responses
		 *
		 * @return IElement[]
		 */
		public function getResponseList(): array;

		/**
		 * get the response by the given request; if it was already executed the response would be returned
		 *
		 * @param IElement $element
		 *
		 * @return IElement
		 */
		public function request(IElement $element): IElement;
	}
