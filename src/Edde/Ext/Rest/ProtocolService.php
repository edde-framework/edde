<?php
	declare(strict_types=1);

	namespace Edde\Ext\Rest;

	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\LazyElementStoreTrait;
	use Edde\Api\Protocol\LazyProtocolManagerTrait;
	use Edde\Api\Store\LazyStoreManagerTrait;
	use Edde\Api\Thread\LazyThreadManagerTrait;
	use Edde\Api\Url\IUrl;
	use Edde\Common\Protocol\Error;
	use Edde\Common\Rest\AbstractService;
	use Edde\Common\Store\FileStore;
	use Edde\Ext\Protocol\ElementContent;
	use Edde\Ext\Session\SessionStore;

	class ProtocolService extends AbstractService {
		use LazyProtocolManagerTrait;
		use LazyElementStoreTrait;
		use LazyStoreManagerTrait;
		use LazyThreadManagerTrait;

		/**
		 * @inheritdoc
		 */
		public function match(IUrl $url): bool {
			return $url->match('~^/api/protocol$~') !== null;
		}

		/**
		 * @inheritdoc
		 */
		public function link($generate, array $parameterList = []) {
			return parent::link('/api/protocol', $parameterList);
		}

		/**
		 * simple request to get current packet
		 *
		 * @param IElement $element
		 */
		public function actionGet(IElement $element) {
			$this->element($element);
			try {
				$this->response(new ElementContent($packet = $this->protocolManager->createPacket()));
				if (($elementId = $element->getMeta('element'))) {
					$reference = new Error(-101, sprintf('Requested element [%s] was not found.', $elementId));
					if ($this->elementStore->has($elementId)) {
						$reference = $this->elementStore->load($elementId);
					}
					$packet->reference($reference);
				} else if (($referenceId = $element->getMeta('reference'))) {
					$packet->references(iterator_to_array($this->elementStore->getReferenceListBy($referenceId)));
				}
				$this->elementStore->save($packet);
			} finally {
				$this->storeManager->restore();
			}
		}

		/**
		 * post packet which will be executed
		 *
		 * @param IElement $element
		 */
		public function actionPost(IElement $element) {
			/**
			 * same stuff for other supported methods
			 */
			$this->element($element);
			try {
				/**
				 * execute incoming packet and return result content
				 */
				$this->response(new ElementContent($element = $this->protocolManager->execute($this->getContent($element, IElement::class))));
				/**
				 * execute thread if it is needed (are there some jobs?)
				 */
				$this->threadManager->execute(['store' => $this->storeManager->getCurrentName()]);
			} finally {
				/**
				 * this is necessary to do to be safe and do not change global context
				 */
				$this->storeManager->restore();
			}
		}

		protected function element(IElement $element) {
			/**
			 * select proper store (session/broadcast)
			 */
			$this->storeManager->select(SessionStore::class);
			if ($element->getMeta('broadcast', false)) {
				$this->storeManager->select(FileStore::class);
			}
		}
	}
