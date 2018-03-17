<?php
	declare(strict_types=1);
	namespace Edde\Inject\Bus;

	use Edde\Bus\IEventBus;

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
