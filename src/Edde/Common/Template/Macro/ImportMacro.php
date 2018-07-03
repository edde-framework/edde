<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\File\IFile;
	use Edde\Api\File\LazyRootDirectoryTrait;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Template\AbstractMacro;

	/**
	 * Import macro will load the given template in compile time.
	 */
	class ImportMacro extends AbstractMacro {
		use LazyRootDirectoryTrait;

		/**
		 * "Real programmers don't comment their code. If it was hard to write, it should be hard to understand."
		 */
		public function __construct() {
			parent::__construct('import');
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 * @throws MacroException
		 */
		public function compile(INode $macro, ICompiler $compiler) {
			$compiler->file($this->file($this->attribute($macro, $compiler, 'src'), $compiler->getCurrent(), $macro));
		}

		/**
		 * execute compilation from the given source
		 *
		 * @param string $src
		 * @param IFile $source
		 * @param INode $macro
		 *
		 * @return IFile
		 * @throws MacroException
		 */
		protected function file(string $src, IFile $source, INode $macro): IFile {
			if (strpos($src, '/') === 0) {
				return $this->rootDirectory->file(substr($src, 1));
			} else if (strpos($src, './') === 0) {
				return $source->getDirectory()
					->file(substr($src, 2));
			}
			throw new MacroException(sprintf('Unknown "src" attribute value [%s] of macro [%s].', $src, $macro->getPath()));
		}
	}
