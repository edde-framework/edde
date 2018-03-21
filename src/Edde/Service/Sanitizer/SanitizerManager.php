<?php
	declare(strict_types=1);
	namespace Edde\Service\Sanitizer;

	use Edde\Sanitizer\ISanitizerManager;

	trait SanitizerManager {
		/**
		 * @var ISanitizerManager
		 */
		protected $sanitizerManager;

		/**
		 * @param ISanitizerManager $sanitizerManager
		 */
		public function lazySanitizerManager(ISanitizerManager $sanitizerManager) {
			$this->sanitizerManager = $sanitizerManager;
		}
	}
