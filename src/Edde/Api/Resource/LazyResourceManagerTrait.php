<?php
	declare(strict_types = 1);

	namespace Edde\Api\Resource;

	/**
	 * Lazy dependency on a resource manager.
	 */
	trait LazyResourceManagerTrait {
		/**
		 * @var IResourceManager
		 */
		protected $resourceManager;

		/**
		 * @param IResourceManager $resourceManager
		 */
		public function lazyResourceManager(IResourceManager $resourceManager) {
			$this->resourceManager = $resourceManager;
		}
	}
