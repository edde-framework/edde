<?php
	declare(strict_types = 1);

	namespace Edde\Api\Event;

	/**
	 * Simple (linear) event bus implementation.
	 */
	interface IEventBus {
		/**
		 * deffered event handler
		 *
		 * @param IHandler $handler
		 *
		 * @return IEventBus
		 */
		public function handler(IHandler $handler): IEventBus;

		/**
		 * register the given "something" as event listener (input should be converted into single listen() calls)
		 *
		 * @param $listen
		 *
		 * @return IEventBus
		 */
		public function listen($listen): IEventBus;

		/**
		 * register event handler
		 *
		 * @param string $event
		 * @param callable $handler
		 *
		 * @return IEventBus
		 */
		public function register(string $event, callable $handler): IEventBus;

		/**
		 * chain events to the given event bus
		 *
		 * @param IEventBus $eventBus
		 *
		 * @return IEventBus
		 */
		public function chain(IEventBus $eventBus): IEventBus;

		/**
		 * execute the callback; the callback can emit event which will be listened only during callback execution
		 *
		 * @param callable $callback
		 * @param array ...$handlerList
		 *
		 * @return mixed result of the callback
		 */
		public function scope(callable $callback, ...$handlerList);

		/**
		 * emit an event to all it's listeners; it should NOT do any magic
		 *
		 * @param IEvent $event
		 * @param string $scope
		 *
		 * @return IEventBus
		 */
		public function event(IEvent $event, string $scope = null): IEventBus;
	}
