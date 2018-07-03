<?php
	declare(strict_types=1);

	namespace Edde\Api\Html;

	trait LazyHtmlGeneratorTrait {
		/**
		 * @var IHtmlGenerator
		 */
		protected $htmlGenerator;

		public function lazyHtmlGenerator(IHtmlGenerator $htmlGenerator) {
			$this->htmlGenerator = $htmlGenerator;
		}
	}
