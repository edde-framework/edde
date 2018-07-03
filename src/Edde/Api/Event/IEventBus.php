<?php
	declare(strict_types=1);

	namespace Edde\Api\Event;

	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\IProtocolHandler;

	interface IEventBus extends IProtocolHandler {
		/**
		 * @param IListener $listener
		 *
		 * @return IEventBus
		 */
		public function register(IListener $listener): IEventBus;

		/**
		 * register listener for the given event
		 *
		 * @param string   $event
		 * @param callable $callback
		 *
		 * @return IEventBus
		 */
		public function listen(string $event, callable $callback): IEventBus;

		/**
		 * immediately emmit the given event
		 *
		 * @param IElement $element
		 *
		 * @return IEventBus
		 */
		public function emit(IElement $element): IEventBus;
	}
