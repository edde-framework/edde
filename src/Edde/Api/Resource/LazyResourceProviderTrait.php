<?php
	declare(strict_types=1);

	namespace Edde\Api\Resource;

	trait LazyResourceProviderTrait {
		/**
		 * @var IResourceProvider
		 */
		protected $resourceProvider;

		/**
		 * @param IResourceProvider $resourceProvider
		 */
		public function lazyResourceProvider(IResourceProvider $resourceProvider) {
			$this->resourceProvider = $resourceProvider;
		}
	}
