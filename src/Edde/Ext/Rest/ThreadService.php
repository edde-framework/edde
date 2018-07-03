<?php
	declare(strict_types=1);

	namespace Edde\Ext\Rest;

	use Edde\Api\Protocol\IElement;
	use Edde\Api\Store\LazyStoreManagerTrait;
	use Edde\Api\Thread\LazyThreadManagerTrait;
	use Edde\Api\Url\IUrl;
	use Edde\Common\Rest\AbstractService;

	class ThreadService extends AbstractService {
		use LazyStoreManagerTrait;
		use LazyThreadManagerTrait;

		/**
		 * @inheritdoc
		 */
		public function match(IUrl $url): bool {
			return $url->match('~^/api/thread~') !== null;
		}

		/**
		 * @inheritdoc
		 */
		public function link($generate, array $parameterList = []) {
			return parent::link('/api/thread', $parameterList);
		}

		/**
		 * head because client should not expect "output" except of headers; in general this method should not return nothing at all because
		 * in general is is a long running task dequeing all current jobs
		 *
		 * @param IElement $element
		 */
		public function actionHead(IElement $element) {
			try {
				if (($store = $element->getMeta('store')) !== null) {
					$this->storeManager->select($store);
				}
				$this->threadManager->pool();
			} finally {
				if ($store) {
					$this->storeManager->restore();
				}
			}
		}
	}
