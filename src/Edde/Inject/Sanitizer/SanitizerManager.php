<?php
	declare(strict_types=1);
	namespace Edde\Inject\Sanitizer;

	use Edde\Sanitizer\ISanitizerManager;

	trait SanitizerManager {
		/**
		 * @var ISanitizerManager
		 */
		protected $sanitizerManager;

		/**
		 * @param \Edde\Sanitizer\ISanitizerManager $sanitizerManager
		 */
		public function lazySanitizerManager(\Edde\Sanitizer\ISanitizerManager $sanitizerManager) {
			$this->sanitizerManager = $sanitizerManager;
		}
	}
