<?php
	declare(strict_types=1);

	namespace Edde\Ext\Cache;

	use Edde\Api\Application\LazyContextTrait;
	use Edde\Api\Cache\ICacheManager;
	use Edde\Common\Config\AbstractConfigurator;

	class ContextCacheManagerConfigurator extends AbstractConfigurator {
		use LazyContextTrait;

		/**
		 * @param ICacheManager $instance
		 */
		public function configure($instance) {
			$instance->setNamespace($this->context->getGuid());
		}
	}
