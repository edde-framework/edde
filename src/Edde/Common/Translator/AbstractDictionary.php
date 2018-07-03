<?php
	declare(strict_types=1);

	namespace Edde\Common\Translator;

	use Edde\Api\Translator\IDictionary;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	/**
	 * Common dictionary implementation.
	 */
	abstract class AbstractDictionary extends Object implements IDictionary {
		use ConfigurableTrait;
		/**
		 * @var string[][]
		 */
		protected $translationList = [];

		/**
		 * @inheritdoc
		 */
		public function translate(string $id, string $language) {
			if (isset($this->translationList[$language][$id]) === false) {
				return null;
			}
			return $this->translationList[$language][$id];
		}

		/**
		 * @inheritdoc
		 */
		public function getWordList() {
			return $this->translationList;
		}
	}
