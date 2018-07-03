<?php
	declare(strict_types = 1);

	namespace Edde\Api\Event;

	/**
	 * Handler implements rules for "extracting" listen methods for an event bus.
	 *
	 * For example something like ReflectionHandler can extract all method which are accepting IEvent interface.
	 */
	interface IHandler extends \IteratorAggregate {
		/**
		 * return handler's scope
		 *
		 * @return string|null
		 */
		public function getScope();

		/**
		 * this should return key as event name and value as handler (event's target) method
		 *
		 * @return \Iterator|callable[]
		 */
		public function getIterator();
	}
