<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\LazyProtocolManagerTrait;
	use Edde\Api\Protocol\LazyProtocolServiceTrait;

	class PacketProtocolHandler extends AbstractProtocolHandler {
		use LazyProtocolManagerTrait;
		use LazyProtocolServiceTrait;

		/**
		 * @inheritdoc
		 */
		public function canHandle(IElement $element): bool {
			return $element->isType('packet');
		}

		/**
		 * @inheritdoc
		 */
		public function onExecute(IElement $element) {
			$referenceList = [];
			/**
			 * execute all elements in packet
			 */
			foreach ($element->getElementList('elements') as $node) {
				/** @var $response IElement */
				if (($response = $this->protocolService->execute($node)) instanceof IElement) {
					/**
					 * all elements must be added as a reference or the receiver side could
					 * accidentally execute elements not to be executed (for example it can die, because
					 * it could not understand Error element)
					 *
					 * so first reference is to current response of the execution, second reference is
					 * to the original element
					 */
					$referenceList[] = ($response->setReference($node));
					$referenceList[] = $node;
				}
			}
			$packet = $this->protocolManager->createPacket($element->getMeta('::store'), $element);
			$packet->references($referenceList);
			return $packet;
		}

		/**
		 * @inheritdoc
		 */
		protected function onQueue(IElement $element) {
			/**
			 * packet handler is working in a bit different way - output must be packet to hold response to the
			 * origin node
			 */
			return $this->protocolService->createPacket($element)->reference($element);
		}
	}
