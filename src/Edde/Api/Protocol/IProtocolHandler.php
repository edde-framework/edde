<?php
	declare(strict_types=1);
	namespace Edde\Api\Protocol;

		use Edde\Api\Config\IConfigurable;
		use Edde\Api\Element\IElement;
		use Edde\Api\Protocol\Exception\IncompatibleElementException;
		use Edde\Api\Protocol\Exception\UnsupportedElementException;

		interface IProtocolHandler extends IConfigurable {
			/**
			 * check if the given element is possible to execute by this protocol handler
			 *
			 * @param IElement $element
			 *
			 * @return bool
			 */
			public function canHandle(IElement $element) : bool;

			/**
			 * check if the element is compatible with this handler
			 *
			 * @param IElement $element
			 *
			 * @return IProtocolHandler
			 *
			 * @throws IncompatibleElementException
			 */
			public function check(IElement $element) : IProtocolHandler;

			/**
			 * execute the given element and return eventual result
			 *
			 * @param IElement $element
			 *
			 * @return IElement|null
			 *
			 * @throws UnsupportedElementException
			 */
			public function execute(IElement $element) : ?IElement;
		}
