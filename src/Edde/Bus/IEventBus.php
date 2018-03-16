<?php
	declare(strict_types=1);
	namespace Edde\Bus;

	interface IEventBus extends IHandler {
		/**
		 * register a listener
		 *
		 * @param IListener $listener
		 *
		 * @return IEventBus
		 */
		public function registerListener(IListener $listener): IEventBus;

		/**
		 * register a handler for the given event
		 *
		 * @param string   $event
		 * @param callable $callback
		 *
		 * @return IEventBus
		 */
		public function listen(string $event, callable $callback): IEventBus;
	}
