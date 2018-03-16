<?php
	declare(strict_types=1);
	namespace Edde\Bus;

	use Edde\Element\Element;
	use Edde\Element\IElement;
	use Edde\Inject\Crypt\RandomService;

	class EventBus extends AbstractHandler implements IEventBus {
		use RandomService;
		/** @var callable[] */
		protected $callbacks = [];

		/** @inheritdoc */
		public function registerListener(IListener $listener): IEventBus {
			foreach ($listener->getListeners() as $event => $listener) {
				$this->listen($event, $listener);
			}
			return $this;
		}

		/** @inheritdoc */
		public function listen(string $event, callable $callback): IEventBus {
			$this->callbacks[$event][] = $callback;
			return $this;
		}

		/** @inheritdoc */
		public function canHandle(IElement $element): bool {
			return $element->getType() === 'event';
		}

		/** @inheritdoc */
		public function execute(IElement $element): ?IElement {
			$this->validate($element);
			$result = new Element('response', $this->randomService->generate());
			$result->setReference($element->getUuid());
			foreach ($this->callbacks[$element->getAttribute('event')] ?? [] as $callback) {
				$callback($element, $result);
			}
			return $result;
		}
	}
