<?php
	declare(strict_types=1);

	namespace Edde\Api\Translator;

	use Edde\Api\Config\IConfigurable;

	/**
	 * Formal source for all words.
	 */
	interface IDictionary extends IConfigurable {
		/**
		 * try to translate a word; if word is not found, null should be returned
		 *
		 * @param string $id
		 * @param string $language requested language
		 *
		 * @return null|string
		 */
		public function translate(string $id, string $language);

		/**
		 * return arra/generator with current set of available words
		 *
		 * @return \Traversable
		 */
		public function getWordList();
	}
