<?php
	declare(strict_types=1);
	namespace Edde\Service\Sanitizer;

	use Edde\Api\Sanitizer\ISanitizer;
	use Edde\Api\Sanitizer\ISanitizerManager;
	use Edde\Exception\Sanitizer\UnknownSanitizerException;
	use Edde\Object;

	class SanitizerManager extends Object implements ISanitizerManager {
		/**
		 * @var ISanitizer[]
		 */
		protected $sanitizers = [];

		/**
		 * @inheritdoc
		 */
		public function registerSanitizer(string $name, ISanitizer $sanitizer): ISanitizerManager {
			$this->sanitizers[$name] = $sanitizer;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function registerSanitizers(array $sanitizers): ISanitizerManager {
			foreach ($sanitizers as $name => $sanitizer) {
				$this->registerSanitizer($name, $sanitizer);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getSanitizer(string $name): ISanitizer {
			if (isset($this->sanitizers[$name]) === false) {
				throw new UnknownSanitizerException(sprintf('Requested unknown sanitizer [%s].', $name));
			}
			return $this->sanitizers[$name];
		}

		/**
		 * @inheritdoc
		 */
		public function sanitize(array $source): array {
			$result = $source;
			foreach ($source as $k => $v) {
				if (isset($this->sanitizers[$k])) {
					$result[$k] = $this->sanitizers[$k]->sanitize($v);
				}
			}
			return $result;
		}
	}
