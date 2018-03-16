<?php
	declare(strict_types=1);
	namespace Edde\Api\Bus;

	use Edde\Bus\BusException;
	use stdClass;

	/**
	 * The core service used for general messaging support (extension of the
	 * original idea of The Protocol implementation, basically this implementation
	 * is the version 2.0).
	 */
	interface IMessageBus extends IHandler {
		/**
		 * register the given message handler
		 *
		 * @param IHandler $handler
		 *
		 * @return IMessageBus
		 */
		public function registerHandler(IHandler $handler): IMessageBus;

		/**
		 * register more message handlers at once
		 *
		 * @param IHandler[] $handlers
		 *
		 * @return IMessageBus
		 */
		public function registerHandlers(array $handlers): IMessageBus;

		/**
		 * get message handler for the given message
		 *
		 * @param IElement $element
		 *
		 * @return IHandler
		 *
		 * @throws BusException
		 */
		public function getHandler(IElement $element): IHandler;

		/**
		 * export element into "standard" class to be serialized to something
		 *
		 * @param IElement $export
		 *
		 * @return stdClass
		 */
		public function export(IElement $export): stdClass;

		/**
		 * convert standard stdClass to an element (without any execution); validation
		 * on elements is run (that means if there is a Message with a lot of elements, whole
		 * message must be valid)
		 *
		 * @param stdClass $import
		 *
		 * @return IElement
		 */
		public function import(stdClass $import): IElement;
	}
