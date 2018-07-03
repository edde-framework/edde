<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Job\Inject\JobManager;
	use Edde\Api\Log\Inject\LogService;
	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\IProtocolHandler;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object\Object;
	use Edde\Common\Protocol\Exception\UnhandledElementException;

	abstract class AbstractProtocolHandler extends Object implements IProtocolHandler {
		use JobManager;
		use LogService;
		use ConfigurableTrait;

		/**
		 * @inheritdoc
		 */
		public function check(IElement $element): IProtocolHandler {
			if ($this->canHandle($element)) {
				return $this;
			}
			throw new UnhandledElementException(sprintf('Unsupported element [%s] in protocol handler [%s].', $element->getName(), static::class));
		}

		/**
		 * @inheritdoc
		 */
		public function execute(IElement $element) {
			if ($element->isAsync()) {
				$element->setMeta('store', true);
				$this->jobManager->queue($element->async(false));
				return $this->onQueue($element);
			}
			return $this->onExecute($element);
		}

		protected function onExecute(IElement $element) {
		}

		protected function onQueue(IElement $element) {
		}
	}
