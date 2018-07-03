<?php
	declare(strict_types=1);

	namespace Edde\Api\Template;

	trait LazyTemplateDirectoryTrait {
		/**
		 * @var ITemplateDirectory
		 */
		protected $templateDirectory;

		public function lazyTemplateDirectory(ITemplateDirectory $templateDirectory) {
			$this->templateDirectory = $templateDirectory;
		}
	}
