<?php
	declare(strict_types=1);

	namespace Edde\Ext\Store;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Store\IStoreManager;
	use Edde\Common\Config\AbstractConfigurator;
	use Edde\Common\Store\FileStore;
	use Edde\Ext\Session\SessionStore;

	class StoreManagerConfigurator extends AbstractConfigurator {
		use LazyContainerTrait;

		/**
		 * @param IStoreManager $instance
		 */
		public function configure($instance) {
			parent::configure($instance);
			$instance->registerStore($this->container->create(FileStore::class, [], __METHOD__));
			$instance->registerStore($this->container->create(SessionStore::class, [], __METHOD__));
			$instance->select(FileStore::class);
		}
	}
