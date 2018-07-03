<?php
	declare(strict_types = 1);

	namespace Edde\Api\Template;

	use Edde\Api\Node\INode;

	/**
	 * HtmlMacro is operating over whole Node.
	 */
	interface IMacro {
		/**
		 * @return string
		 */
		public function getName(): string;

		/**
		 * @return bool
		 */
		public function hasHelperSet(): bool;

		/**
		 * @return IHelperSet
		 */
		public function getHelperSet(): IHelperSet;

		/**
		 * pre-compile preparation of node tree; may return the same or a new macro which will be used as a new root
		 *
		 * @param INode $macro
		 * @param ICompiler $compiler
		 *
		 * @return INode|null
		 */
		public function inline(INode $macro, ICompiler $compiler);

		/**
		 * executed in compile time
		 *
		 * @param INode $macro
		 * @param ICompiler $compiler
		 *
		 * @return mixed
		 */
		public function compile(INode $macro, ICompiler $compiler);

		/**
		 * executed in runtime phase
		 *
		 * @param INode $macro
		 * @param ICompiler $compiler
		 */
		public function macro(INode $macro, ICompiler $compiler);
	}
