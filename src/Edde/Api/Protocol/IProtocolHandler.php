<?php
	namespace Edde\Api\Protocol;

		use Edde\Api\Config\IConfigurable;
		use Edde\Api\Element\IElement;
		use Edde\Api\Protocol\Exception\UnsupportedElementException;

		interface IProtocolHandler extends IConfigurable {
			/**
			 * check if this protocol handler accepts the given element (just formal check; more deep
			 * check should be done in canHandle()); this should check only element type
			 *
			 * @param IElement $element
			 *
			 * @return bool
			 */
			public function accept(IElement $element): bool;

			/**
			 * check if the given element is possible to execute by this protocol handler
			 *
			 * @param IElement $element
			 *
			 * @return bool
			 */
			public function canHandle(IElement $element): bool;

			/**
			 * execute the given element and return eventual result
			 *
			 * @param IElement $element
			 *
			 * @return IElement|null
			 *
			 * @throws UnsupportedElementException
			 */
			public function execute(IElement $element): ?IElement;
		}
