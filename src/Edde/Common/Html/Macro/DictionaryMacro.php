<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\File\FileException;
	use Edde\Api\File\IFile;
	use Edde\Api\File\LazyRootDirectoryTrait;
	use Edde\Api\LazyAssetsDirectoryTrait;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;
	use Edde\Common\File\File;

	/**
	 * Dictionary macro support.
	 */
	class DictionaryMacro extends AbstractHtmlMacro {
		use LazyRootDirectoryTrait;
		use LazyAssetsDirectoryTrait;

		/**
		 * How can you tell you have a really bad case of acne?
		 *
		 * It’s when the blind try to read your face.
		 */
		public function __construct() {
			parent::__construct('dictionary');
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 * @throws FileException
		 * @throws MacroException
		 */
		public function compile(INode $macro, ICompiler $compiler) {
			$macro->setAttribute('src', $this->file($this->attribute($macro, $compiler, 'src'), $compiler->getCurrent())
				->getPath());
			if ($scope = ($macro->hasAttribute('scope') ? $macro->getAttribute('scope') : $compiler->getVariable('scope'))) {
				$macro->setAttribute('scope', ($helper = $compiler->helper($macro, $scope)) ? $helper : $scope);
			}
		}

		/** @noinspection PhpMissingParentCallCommonInspection */

		/**
		 * resolve css file include
		 *
		 * @param string $src
		 * @param IFile $source
		 *
		 * @return IFile
		 * @throws FileException
		 */
		protected function file(string $src, IFile $source): IFile {
			if (strpos($src, '/') === 0) {
				return $this->rootDirectory->file(substr($src, 1));
			} else if (strpos($src, './') === 0) {
				return $source->getDirectory()
					->file(substr($src, 2));
			} else if (strpos($src, 'edde://') !== false) {
				return $this->assetsDirectory->file(str_replace('edde://', '', $src));
			}
			return new File($src);
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 * @throws MacroException
		 */
		public function macro(INode $macro, ICompiler $compiler) {
			$this->write($compiler, sprintf('$this->translator->registerSource(new %s(%s), %s);', File::class, var_export($this->attribute($macro, $compiler, 'src', false), true), var_export($macro->getAttribute('scope'), true)), 5);
		}
	}
