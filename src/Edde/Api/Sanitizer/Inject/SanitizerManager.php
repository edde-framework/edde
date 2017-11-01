<?php
	declare(strict_types=1);
	namespace Edde\Api\Sanitizer\Inject;

		use Edde\Api\Sanitizer\ISanitizerManager;

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
