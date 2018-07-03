<?php
	declare(strict_types=1);

	namespace Edde\Api\Event\Inject;

	use Edde\Api\Event\IEventBus;

	trait EventBus {
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
