<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\IProtocolManager;
	use Edde\Api\Store\LazyStoreManagerTrait;
	use Edde\Common\Object\Object;
	use Edde\Ext\Session\SessionStore;

	abstract class AbstractProtocolManager extends Object implements IProtocolManager {
		use \Edde\Api\Protocol\Inject\ProtocolService;
		use LazyStoreManagerTrait;
		const ELEMENT_LIST_ID = 'protocol-manager/element-list';

		/**
		 * @inheritdoc
		 */
		public function queue(IElement $element, string $store = null): IProtocolManager {
			$this->storeManager->select($store ?: SessionStore::class);
			try {
				$this->storeManager->block(self::ELEMENT_LIST_ID);
				$elementList = $this->storeManager->get(self::ELEMENT_LIST_ID, []);
				$elementList[$element->getId()] = $element;
				$this->storeManager->set(self::ELEMENT_LIST_ID, $elementList);
			} finally {
				$this->storeManager->restore();
				$this->storeManager->unlock(self::ELEMENT_LIST_ID);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function queueList(array $elementList, string $store = null): IProtocolManager {
			foreach ($elementList as $element) {
				$this->queue($element, $store);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function createPacket(string $store = null, IElement $reference = null): IElement {
			$this->storeManager->select($store ?: SessionStore::class);
			try {
				$this->storeManager->block(self::ELEMENT_LIST_ID);
				$packet = $this->protocolService->createPacket($reference);
				$packet->setElementList('elements', $this->storeManager->get(self::ELEMENT_LIST_ID, []));
				$this->storeManager->remove(self::ELEMENT_LIST_ID);
				return $packet;
			} finally {
				$this->storeManager->restore();
				$this->storeManager->unlock(self::ELEMENT_LIST_ID);
			}
		}

		/**
		 * @inheritdoc
		 */
		public function execute(IElement $element): IElement {
			return $this->protocolService->execute($element);
		}
	}
