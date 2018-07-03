<?php
	declare(strict_types=1);

	namespace Edde\Common\Event;

	use Edde\Api\Event\IEventBus;
	use Edde\Api\Event\IListener;
	use Edde\Api\Protocol\IElement;
	use Edde\Common\Protocol\AbstractProtocolHandler;

	class EventBus extends AbstractProtocolHandler implements IEventBus {
		/**
		 * @var callable[]
		 */
		protected $callbackList = [];

		/**
		 * @inheritdoc
		 */
		public function register(IListener $listener): IEventBus {
			foreach ($listener->getListenerList() as $event => $listener) {
				$this->listen($event, $listener);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function listen(string $event, callable $callback): IEventBus {
			$this->callbackList[$event][] = $callback;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function canHandle(IElement $element): bool {
			return $element->isType('event');
		}

		/**
		 * @inheritdoc
		 */
		public function onExecute(IElement $element) {
			if (isset($this->callbackList[$type = $element->getAttribute('event')])) {
				foreach ($this->callbackList[$type] as $callback) {
					$callback($element);
				}
			}
		}

		/**
		 * @inheritdoc
		 */
		public function emit(IElement $element): IEventBus {
			$this->execute($element);
			return $this;
		}
	}
