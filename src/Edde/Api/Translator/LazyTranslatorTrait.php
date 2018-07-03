<?php
	declare(strict_types=1);

	namespace Edde\Api\Translator;

	/**
	 * Translator dependency.
	 */
	trait LazyTranslatorTrait {
		/**
		 * @var ITranslator
		 */
		protected $translator;

		/**
		 * @param ITranslator $translator
		 */
		public function lazyTranslator(ITranslator $translator) {
			$this->translator = $translator;
		}
	}
