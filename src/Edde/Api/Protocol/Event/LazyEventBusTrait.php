<?php
	declare(strict_types=1);

	namespace Edde\Api\Protocol\Event;

	trait LazyEventBusTrait {
		/**
		 * @var IEventBus
		 */
		protected $eventBus;

		/**
		 * @param IEventBus $eventBus
		 */
		public function lazyEventBus(IEventBus $eventBus) {
			$this->eventBus = $eventBus;
		}
	}
