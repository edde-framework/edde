<?php
	declare(strict_types=1);
	namespace Edde\Api\Bus;

	use Edde\Api\Config\IConfigurable;
	use Edde\Exception\Bus\InvalidElementException;
	use Edde\Exception\Container\ContainerException;
	use Edde\Exception\Validator\ValidationException;

	/**
	 * Element handler is able to handle individual type of an element (event, request, ...).
	 */
	interface IHandler extends IConfigurable {
		/**
		 * when true is returned, handler must be able properly handle the
		 * given element; the handler still could throw an exception
		 * during element validation
		 *
		 * @param IElement $element
		 *
		 * @return bool
		 */
		public function canHandle(IElement $element): bool;

		/**
		 * @param IElement $element
		 *
		 * @throws InvalidElementException
		 * @throws ValidationException
		 */
		public function validate(IElement $element): void;

		/**
		 * send a message; message could me enqued or actually executed
		 *
		 * @param IElement $element
		 *
		 * @return IElement
		 *
		 * @throws InvalidElementException
		 * @throws ValidationException
		 */
		public function send(IElement $element): IElement;

		/**
		 * actually executes the given element (for example queued element, async, ...)
		 *
		 * @param IElement $element
		 *
		 * @return IElement|null
		 *
		 * @throws InvalidElementException
		 * @throws ValidationException
		 * @throws ContainerException
		 */
		public function execute(IElement $element): ?IElement;
	}
