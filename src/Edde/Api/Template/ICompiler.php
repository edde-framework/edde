<?php
	declare(strict_types = 1);

	namespace Edde\Api\Template;

	use Edde\Api\Container\ILazyInject;
	use Edde\Api\File\IFile;
	use Edde\Api\Node\INode;

	interface ICompiler extends ILazyInject {
		/**
		 * use the given macroset
		 *
		 * @param IMacroSet $macroSet
		 *
		 * @return ICompiler
		 */
		public function registerMacroSet(IMacroSet $macroSet): ICompiler;

		/**
		 * register the given helper set
		 *
		 * @param IHelperSet $helperSet
		 *
		 * @return ICompiler
		 */
		public function registerHelperSet(IHelperSet $helperSet): ICompiler;

		/**
		 * "runtime macro" - those should generate runtime
		 *
		 * @param IMacro $macro
		 *
		 * @return ICompiler
		 */
		public function registerMacro(IMacro $macro): ICompiler;

		/**
		 * process inline macros first (may modify macro node tree)
		 *
		 * @param INode $macro
		 *
		 * @return ICompiler
		 */
		public function inline(INode $macro): ICompiler;

		/**
		 * execute compile macro
		 *
		 * @param INode $macro
		 *
		 * @return
		 * @internal param INode $root
		 *
		 */
		public function compile(INode $macro);

		/**
		 * execute macro in "runtime"
		 *
		 * @param INode $macro
		 */
		public function macro(INode $macro);

		/**
		 * compile source into node; node is the final result
		 *
		 * @param IFile $file
		 *
		 * @return INode
		 * @internal param INode $root
		 */
		public function file(IFile $file): INode;

		/**
		 * return the original source file
		 *
		 * @return IFile
		 */
		public function getSource(): IFile;

		/**
		 * return has of currently used files (same per file set including imports)
		 *
		 * @return string
		 */
		public function getHash(): string;

		/**
		 * return array of current import list
		 *
		 * @return string[]
		 */
		public function getImportList(): array;

		/**
		 * if there are embedded templates, this method return current template file
		 *
		 * @return IFile
		 */
		public function getCurrent(): IFile;

		/**
		 * layout is the root (first file - getSource() === getCurrent())
		 *
		 * @return bool
		 */
		public function isLayout(): bool;

		/**
		 * build a final template; import list can contain additional set of templates (loaded before the main one)
		 *
		 * @param IFile[] $importList
		 *
		 * @return mixed
		 */
		public function template(array $importList = []);

		/**
		 * add a value to compiler context
		 *
		 * @param string $name
		 * @param mixed $value
		 *
		 * @return ICompiler
		 */
		public function setVariable(string $name, $value): ICompiler;

		/**
		 * retrieve the given value from compiler's context
		 *
		 * @param string $name
		 * @param null $default
		 *
		 * @return mixed
		 */
		public function getVariable(string $name, $default = null);

		/**
		 * execute all available helpers agains the given value of attribute
		 *
		 * @param INode $macro
		 * @param string $value
		 *
		 * @return null|string
		 */
		public function helper(INode $macro, $value);

		/**
		 * block under the given id
		 *
		 * @param string $name
		 * @param INode $block
		 *
		 * @return ICompiler
		 */
		public function block(string $name, INode $block): ICompiler;

		/**
		 * return list of nodes by the given block name
		 *
		 * @param string $name
		 *
		 * @return INode
		 */
		public function getBlock(string $name): INode;

		/**
		 * retrieve list of registered blocks
		 *
		 * @return INode[]
		 */
		public function getBlockList(): array;
	}
