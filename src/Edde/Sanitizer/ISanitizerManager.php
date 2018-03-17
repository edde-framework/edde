<?php
	declare(strict_types=1);
	namespace Edde\Sanitizer;

	use Edde\Config\IConfigurable;

	/**
	 * Sanitizer is responsible for filtering values going OUT of PHP, for example
	 * values being stored in database (like date time, ...) and others. It can be
	 * eventually even used for http request filtering.
	 */
	interface ISanitizerManager extends IConfigurable {
		/**
		 * register the given sanitizer
		 *
		 * @param string     $name
		 * @param ISanitizer $sanitizer
		 *
		 * @return ISanitizerManager
		 */
		public function registerSanitizer(string $name, ISanitizer $sanitizer): ISanitizerManager;

		/**
		 * register list of sanitizers
		 *
		 * @param ISanitizer[] $sanitizers
		 *
		 * @return ISanitizerManager
		 */
		public function registerSanitizers(array $sanitizers): ISanitizerManager;

		/**
		 * @param string $name
		 *
		 * @return ISanitizer
		 *
		 * @throws SanitizerException
		 */
		public function getSanitizer(string $name): ISanitizer;

		/**
		 * filter the given input array into output array; all known sanitizers are applied
		 *
		 * @param array $source
		 *
		 * @return array
		 */
		public function sanitize(array $source): array;
	}
