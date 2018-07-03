<?php
	declare(strict_types=1);

	namespace Edde\Api\Link;

	/**
	 * Lazy link cache trait depenendency.
	 */
	trait LazyLinkFactoryTrait {
		/**
		 * @var ILinkFactory
		 */
		protected $linkFactory;

		/**
		 * @param ILinkFactory $linkFactory
		 */
		public function lazyLinkFactory(ILinkFactory $linkFactory) {
			$this->linkFactory = $linkFactory;
		}
	}
