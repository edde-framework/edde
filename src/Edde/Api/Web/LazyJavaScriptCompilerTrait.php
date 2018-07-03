<?php
	declare(strict_types = 1);

	namespace Edde\Api\Web;

	/**
	 * Lazy javascript compiler dependency.
	 */
	trait LazyJavaScriptCompilerTrait {
		/**
		 * @var IJavaScriptCompiler
		 */
		protected $javaScriptCompiler;

		/**
		 * @param IJavaScriptCompiler $javaScriptCompiler
		 */
		public function lazyJavaScriptCompiler(IJavaScriptCompiler $javaScriptCompiler) {
			$this->javaScriptCompiler = $javaScriptCompiler;
		}
	}
