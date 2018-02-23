<?php
	declare(strict_types=1);
	namespace Edde\Api\Bus\Inject;

	use Edde\Api\Bus\Event\IEventBus;

	trait EventBus {
		/** @var IEventBus */
		protected $eventBus;

		/**
		 * @param IEventBus $eventBus
		 */
		public function lazyEventBus(IEventBus $eventBus): void {
			$this->eventBus = $eventBus;
		}
	}
