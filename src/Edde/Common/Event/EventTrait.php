<?php
	declare(strict_types = 1);

	namespace Edde\Common\Event;

	use Edde\Api\Event\IEvent;
	use Edde\Api\Event\IEventBus;
	use Edde\Api\Event\IHandler;

	trait EventTrait {
		/**
		 * local event bus from a EventTrait
		 *
		 * @var IEventBus
		 */
		protected $traitEventBus;

		public function handler(IHandler $handler): IEventBus {
			if ($this->traitEventBus === null) {
				$this->traitEventBus = new EventBus();
			}
			return $this->traitEventBus->handler($handler);
		}

		public function listen($listen): IEventBus {
			if ($this->traitEventBus === null) {
				$this->traitEventBus = new EventBus();
			}
			return $this->traitEventBus->listen($listen);
		}

		public function register(string $event, callable $handler): IEventBus {
			if ($this->traitEventBus === null) {
				$this->traitEventBus = new EventBus();
			}
			return $this->traitEventBus->register($event, $handler);
		}

		public function chain(IEventBus $eventBus): IEventBus {
			if ($this->traitEventBus === null) {
				$this->traitEventBus = new EventBus();
			}
			return $this->traitEventBus->chain($eventBus);
		}

		public function scope(callable $callback, ...$handlerList): IEventBus {
			if ($this->traitEventBus === null) {
				$this->traitEventBus = new EventBus();
			}
			return $this->traitEventBus->scope($callback, ...$handlerList);
		}

		public function event(IEvent $event, string $scope = null): IEventBus {
			if ($this->traitEventBus === null) {
				$this->traitEventBus = new EventBus();
			}
			return $this->traitEventBus->event($event, $scope);
		}
	}
