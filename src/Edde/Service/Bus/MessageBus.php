<?php
	declare(strict_types=1);
	namespace Edde\Service\Bus;

	use Edde\Api\Bus\IElement;
	use Edde\Api\Bus\IHandler;
	use Edde\Api\Bus\IMessageBus;
	use Edde\Common\Bus\AbstractHandler;
	use Edde\Common\Bus\Element;
	use Edde\Common\Bus\Error;
	use Edde\Exception\Bus\UnhandledElementException;
	use Edde\Inject\Crypt\RandomService;
	use stdClass;

	class MessageBus extends AbstractHandler implements IMessageBus {
		use RandomService;
		/** @var IHandler[] */
		protected $handlers = [];
		/** @var IHandler[] */
		protected $knows = [];

		/** @inheritdoc */
		public function registerHandler(IHandler $handler): IMessageBus {
			$this->handlers[] = $handler;
			return $this;
		}

		/** @inheritdoc */
		public function registerHandlers(array $handlers): IMessageBus {
			foreach ($handlers as $handler) {
				$this->registerHandler($handler);
			}
			return $this;
		}

		/** @inheritdoc */
		public function getHandler(IElement $element): IHandler {
			if (isset($this->knows[$type = $element->getType()])) {
				return $this->knows[$type];
			}
			foreach ($this->handlers as $handler) {
				if ($handler->canHandle($element)) {
					return $this->knows[$type] = $handler;
				}
			}
			throw new UnhandledElementException(sprintf('Cannot handle element [%s (%s)] in any handler; element type is not supported.', $element->getType(), get_class($element)));
		}

		/** @inheritdoc */
		public function export(IElement $export): stdClass {
			$object = [
				'type'       => $export->getType(),
				'uuid'       => $export->getUuid(),
				'target'     => $export->getTarget(),
				'reference'  => $export->getReference(),
				'attributes' => $export->getAttributes(),
				'metas'      => $export->getMetas(),
			];
			foreach ($export->getSends() as $k => $element) {
				$object['sends'][$k] = $this->export($element);
			}
			foreach ($export->getExecutes() as $k => $element) {
				$object['executes'][$k] = $this->export($element);
			}
			foreach ($export->getResponses() as $k => $element) {
				$object['responses'][$k] = $this->export($element);
			}
			return (object)$object;
		}

		/** @inheritdoc */
		public function import(stdClass $import): IElement {
			$element = new Element(
				$import->type ?? 'unknown',
				$import->uuid ?? '',
				isset($import->attributes) ? (array)$import->attributes : [],
				isset($import->metas) ? (array)$import->metas : []
			);
			if (isset($import->target)) {
				$element->setTarget($import->target);
			}
			if (isset($import->reference)) {
				$element->setReference($import->reference);
			}
			foreach ($import->sends ?? [] as $object) {
				$element->send($this->import($object));
			}
			foreach ($import->executes ?? [] as $object) {
				$element->execute($this->import($object));
			}
			foreach ($import->responses ?? [] as $k => $object) {
				$element->response($k, $this->import($object));
			}
			$this->validate($element);
			return $element;
		}

		/** @inheritdoc */
		public function canHandle(IElement $element): bool {
			return $this->getHandler($element)->canHandle($element);
		}

		/** @inheritdoc */
		public function send(IElement $element): IElement {
			return $this->getHandler($element)->send($element);
		}

		/** @inheritdoc */
		public function execute(IElement $element): ?IElement {
			try {
				return $this->getHandler($element)->execute($element);
			} catch (\Exception $exception) {
				return new Error($exception->getMessage(), $this->randomService->uuid(), $exception->getCode(), get_class($exception));
			}
		}
	}
