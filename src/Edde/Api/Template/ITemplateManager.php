<?php
	declare(strict_types=1);

	namespace Edde\Api\Template;

	interface ITemplateManager {
		/**
		 * @return ITemplate
		 */
		public function template(): ITemplate;
	}
