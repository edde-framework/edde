<?php
	declare(strict_types=1);

	namespace Edde\Ext\Thread;

	use Edde\Api\Link\LazyLinkFactoryTrait;
	use Edde\Api\Url\UrlException;
	use Edde\Common\Config\AbstractConfigurator;
	use Edde\Common\Thread\WebExecutor;
	use Edde\Ext\Rest\ThreadService;

	class WebExecutorConfigurator extends AbstractConfigurator {
		use LazyLinkFactoryTrait;

		/**
		 * @param WebExecutor $instance
		 *
		 * @throws UrlException
		 */
		public function configure($instance) {
			parent::configure($instance);
			$instance->setUrl($this->linkFactory->link(ThreadService::class));
		}
	}
