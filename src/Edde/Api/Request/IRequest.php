<?php
	declare(strict_types=1);
	namespace Edde\Api\Request;

		use Edde\Api\Element\IElement;

		/**
		 * Marker interface for a request element of an application. There should be only
		 * one request element per time from an user interaction. This interface is not intended
		 * to be used for jobs or more requests per time to an application pipeline as everything
		 * should go through The Protocol (including this packet).
		 */
		interface IRequest {
			/**
			 * return current element being executed
			 *
			 * @return IElement
			 */
			public function getElement(): IElement;

			/**
			 * return list of types (could be mime type) to which conversion should be done
			 *
			 * @return string[]
			 */
			public function getTargetList(): array;
		}
