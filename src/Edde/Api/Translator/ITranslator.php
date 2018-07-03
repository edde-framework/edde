<?php
	declare(strict_types=1);

	namespace Edde\Api\Translator;

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\File\IFile;

	/**
	 * Implementation of a translator.
	 */
	interface ITranslator extends IConfigurable {
		/**
		 * register source to translator; this will be convertet via converter to target dictionary (and registered with register
		 *
		 * @param IFile       $source
		 * @param string|null $scope
		 *
		 * @return ITranslator
		 */
		public function registerSource(IFile $source, string $scope = null): ITranslator;

		/**
		 * register source of words
		 *
		 * @param IDictionary $dictionary
		 * @param string      $scope
		 *
		 * @return ITranslator
		 */
		public function registerDictionary(IDictionary $dictionary, string $scope = null): ITranslator;

		/**
		 * language can be set in a runtime
		 *
		 * @param string $language
		 *
		 * @return ITranslator
		 */
		public function setLanguage(string $language): ITranslator;

		/**
		 * ability to "push" a scope on stack (later can be popped)
		 *
		 * @param string|null $scope
		 *
		 * @return ITranslator
		 */
		public function pushScope(string $scope = null): ITranslator;

		/**
		 * pops the current scope from stack and return a previous one
		 *
		 * @return ITranslator
		 */
		public function popScope(): ITranslator;

		/**
		 * try to translate a string
		 *
		 * @param string      $name
		 * @param string      $scope override current scope
		 * @param string|null $language
		 *
		 * @return string
		 */
		public function translate(string $name, string $scope = null, string $language = null): string;
	}
