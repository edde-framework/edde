<?php
	declare(strict_types=1);
	namespace Edde\Service\Sanitizer;

	use Edde\Exception\Sanitizer\UnknownSanitizerException;
	use Edde\Object;
	use Edde\Sanitizer\ISanitizer;

	class SanitizerManager extends Object implements \Edde\Sanitizer\ISanitizerManager {
		/**
		 * @var \Edde\Sanitizer\ISanitizer[]
		 */
		protected $sanitizers = [];

		/**
		 * @inheritdoc
		 */
		public function registerSanitizer(string $name, \Edde\Sanitizer\ISanitizer $sanitizer): \Edde\Sanitizer\ISanitizerManager {
			$this->sanitizers[$name] = $sanitizer;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function registerSanitizers(array $sanitizers): \Edde\Sanitizer\ISanitizerManager {
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
