<?php
	declare(strict_types=1);
	namespace Edde\Common\Protocol;

		use Edde\Api\Element\IElement;
		use Edde\Api\Protocol\Exception\UnsupportedElementException;
		use Edde\Api\Protocol\IProtocolHandler;
		use Edde\Api\Protocol\IProtocolService;

		class ProtocolService extends AbstractProtocolHandler implements IProtocolService {
			/**
			 * @var IProtocolHandler[]
			 */
			protected $protocolHandlerList = [];
			/**
			 * @var IProtocolHandler[]
			 */
			protected $protocolHandlerCache = [];

			/**
			 * @inheritdoc
			 */
			public function registerProtocolHandler(IProtocolHandler $protocolHandler): IProtocolService {
				$this->protocolHandlerList[] = $protocolHandler;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function registerProtocolHandlerList(array $protocolHandlerList): IProtocolService {
				foreach ($protocolHandlerList as $protocolHandler) {
					$this->registerProtocolHandler($protocolHandler);
				}
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function accept(IElement $element): bool {
				foreach ($this->protocolHandlerList as $protocolHandler) {
					if ($protocolHandler->setup() && $protocolHandler->accept($element)) {
						return true;
					}
				}
				return false;
			}

			/**
			 * @inheritdoc
			 */
			public function canHandle(IElement $element): bool {
				foreach ($this->protocolHandlerList as $protocolHandler) {
					if ($protocolHandler->setup() && $protocolHandler->canHandle($element)) {
						return true;
					}
				}
				return false;
			}

			/**
			 * @inheritdoc
			 */
			public function execute(IElement $element): ?IElement {
				if (isset($this->protocolHandlerCache[$type = $element->getType()])) {
					return $this->protocolHandlerCache[$type]->execute($element);
				}
				foreach ($this->protocolHandlerList as $protocolHandler) {
					if ($protocolHandler->setup() && $protocolHandler->canHandle($element)) {
						return ($this->protocolHandlerCache[$type] = $protocolHandler)->execute($element);
					}
				}
				throw new UnsupportedElementException(sprintf('Given element type [%s] is not supported by current protocol service.', $type));
			}
		}
