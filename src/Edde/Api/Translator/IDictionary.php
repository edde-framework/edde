<?php
	declare(strict_types = 1);

	namespace Edde\Api\Translator;

	/**
	 * Formal source for all words.
	 */
	interface IDictionary {
		/**
		 * try to translate a word; if word is not found, null should be returned
		 *
		 * @param string $id
		 * @param string $language requested language
		 *
		 * @return null|string
		 */
		public function translate(string $id, string $language);
	}
