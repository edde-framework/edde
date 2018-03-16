<?php
	declare(strict_types=1);
	namespace Edde\Inject\Bus;

	use Edde\Bus\IEventBus;

	trait EventBus {
		/** @var \Edde\Bus\Event\IEventBus */
		protected $eventBus;

		/**
		 * @param \Edde\Bus\IEventBus $eventBus
		 */
		public function lazyEventBus(IEventBus $eventBus): void {
			$this->eventBus = $eventBus;
		}
	}
