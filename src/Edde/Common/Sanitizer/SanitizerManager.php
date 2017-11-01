<?php
	declare(strict_types=1);
	namespace Edde\Common\Sanitizer;

		use Edde\Api\Sanitizer\Exception\UnknownSanitizerException;
		use Edde\Api\Sanitizer\ISanitizer;
		use Edde\Api\Sanitizer\ISanitizerManager;
		use Edde\Common\Object\Object;

		class SanitizerManager extends Object implements ISanitizerManager {
			/**
			 * @var ISanitizer[]
			 */
			protected $sanitizerList = [];

			/**
			 * @inheritdoc
			 */
			public function registerSanitizer(string $name, ISanitizer $sanitizer): ISanitizerManager {
				$this->sanitizerList[$name] = $sanitizer;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function registerSanitizerList(array $sanitizerList): ISanitizerManager {
				foreach ($sanitizerList as $name => $sanitizer) {
					$this->registerSanitizer($name, $sanitizer);
				}
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function getSanitizer(string $name): ISanitizer {
				if (isset($this->sanitizerList[$name]) === false) {
					throw new UnknownSanitizerException(sprintf('Requested unknown sanitizer [%s].', $name));
				}
				return $this->sanitizerList[$name];
			}

			/**
			 * @inheritdoc
			 */
			public function sanitize(array $source): array {
				$result = $source;
				foreach ($source as $k => $v) {
					if (isset($this->sanitizerList[$k])) {
						$result[$k] = $this->sanitizerList[$k]->sanitize($v);
					}
				}
				return $result;
			}
		}
