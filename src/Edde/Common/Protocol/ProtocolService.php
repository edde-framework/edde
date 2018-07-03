<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Http\Inject\HostUrl;
	use Edde\Api\Log\Inject\LogService;
	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\IProtocolHandler;
	use Edde\Api\Protocol\IProtocolService;
	use Edde\Common\Protocol\Exception\UnhandledElementException;

	class ProtocolService extends AbstractProtocolHandler implements IProtocolService {
		use HostUrl;
		use LogService;
		/**
		 * @var IProtocolHandler[]
		 */
		protected $protocolHandlerList = [];
		/**
		 * @var IProtocolHandler[]
		 */
		protected $handleList = [];

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
		public function createPacket(IElement $reference = null, string $origin = null): IElement {
			return (new Packet($origin ?: $this->hostUrl->getAbsoluteUrl()))->setReference($reference);
		}

		/**
		 * @inheritdoc
		 */
		public function canHandle(IElement $element): bool {
			try {
				return $this->getProtocolHandler($element)->canHandle($element);
			} catch (\Exception $exception) {
				return false;
			}
		}

		/**
		 * @inheritdoc
		 */
		public function execute(IElement $element) {
			try {
				return $response = $this->getProtocolHandler($element)
					->execute($element);
			} catch (\Throwable $exception) {
				$response = new Error(-102, $exception->getMessage());
				$response->setException(get_class($exception));
				$response->setReference($element);
				$this->logService->exception($exception, [
					'edde',
					'protocol',
				]);
				return $response;
			}
		}

		/**
		 * @param IElement $element
		 *
		 * @return IProtocolHandler
		 * @throws UnhandledElementException
		 */
		protected function getProtocolHandler(IElement $element): IProtocolHandler {
			if (isset($this->handleList[$type = $element->getType()])) {
				return $this->handleList[$type];
			}
			foreach ($this->protocolHandlerList as $protocolHandler) {
				if ($protocolHandler->setup() && $protocolHandler->canHandle($element)) {
					return $this->handleList[$type] = $protocolHandler;
				}
			}
			throw new UnhandledElementException(sprintf('The given element is not supported or cannot be handled [%s].', $type));
		}
	}
