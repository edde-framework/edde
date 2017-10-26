<?php
	namespace Edde\Api\Sanitizer;

		use Edde\Api\Config\IConfigurable;
		use Edde\Api\Sanitizer\Exception\UnknownSanitizerException;

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
			 * @param ISanitizer[] $sanitizerList
			 *
			 * @return ISanitizerManager
			 */
			public function registerSanitizerList(array $sanitizerList): ISanitizerManager;

			/**
			 * @param string $name
			 *
			 * @return ISanitizer
			 *
			 * @throws UnknownSanitizerException
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