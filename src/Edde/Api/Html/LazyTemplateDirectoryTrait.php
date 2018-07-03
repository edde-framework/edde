<?php
	declare(strict_types = 1);

	namespace Edde\Api\Html;

	/**
	 * Lazy template directory dependency.
	 */
	trait LazyTemplateDirectoryTrait {
		/**
		 * @var ITemplateDirectory
		 */
		protected $templateDirectory;

		/**
		 * @param ITemplateDirectory $templateDirectory
		 */
		public function lazyTemplateDirectory(ITemplateDirectory $templateDirectory) {
			$this->templateDirectory = $templateDirectory;
		}
	}
