<?php
	declare(strict_types = 1);

	namespace Edde\Api\Web;

	/**
	 * Lazy stylesheet compiler trait.
	 */
	trait LazyStyleSheetCompilerTrait {
		/**
		 * @var IStyleSheetCompiler
		 */
		protected $styleSheetCompiler;

		/**
		 * @param IStyleSheetCompiler $styleSheetCompiler
		 */
		public function lazyStyleSheetCompiler(IStyleSheetCompiler $styleSheetCompiler) {
			$this->styleSheetCompiler = $styleSheetCompiler;
		}
	}
