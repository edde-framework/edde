<?php
	declare(strict_types=1);

	namespace Edde\Api\Template;

	trait LazyCompilerTrait {
		/**
		 * @var ICompiler
		 */
		protected $compiler;

		/**
		 * @param ICompiler $compiler
		 */
		public function lazyCompiler(ICompiler $compiler) {
			$this->compiler = $compiler;
		}
	}
