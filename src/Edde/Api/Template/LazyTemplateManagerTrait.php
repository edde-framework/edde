<?php
	declare(strict_types=1);

	namespace Edde\Api\Template;

	trait LazyTemplateManagerTrait {
		/**
		 * @var ITemplateManager
		 */
		protected $templateManager;

		/**
		 * @param ITemplateManager $templateManager
		 */
		public function lazyTemplateManager(ITemplateManager $templateManager) {
			$this->templateManager = $templateManager;
		}
	}
