<?php
	declare(strict_types=1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\File\FileException;
	use Edde\Api\File\IFile;
	use Edde\Api\File\LazyRootDirectoryTrait;
	use Edde\Api\LazyAssetsDirectoryTrait;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Resource\Resource;
	use Edde\Common\Url\Url;

	/**
	 * Css support macro; this will generate item to styleSheetList property in abstract template.
	 */
	class CssMacro extends AbstractHtmlMacro {
		use LazyRootDirectoryTrait;
		use LazyAssetsDirectoryTrait;

		/**
		 * Any sufficiently advanced bug is indistinguishable from a feature.
		 */
		public function __construct() {
			parent::__construct('css');
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 * @throws FileException
		 * @throws MacroException
		 */
		public function compile(INode $macro, ICompiler $compiler) {
			$compiled = $compiler->helper($macro, $file = $this->file($this->attribute($macro, $compiler, 'src', false), $compiler->getCurrent()));
			$macro->setAttribute('src', $compiled ?: var_export($file, true));
		}

		/**
		 * resolve css file include
		 *
		 * @param string $src
		 * @param IFile  $source
		 *
		 * @return IFile|string
		 * @throws FileException
		 */
		protected function file(string $src, IFile $source) {
			if (strpos($src, '/') === 0) {
				return $this->rootDirectory->file(substr($src, 1))
					->getPath();
			} else if (strpos($src, './') === 0) {
				return $source->getDirectory()
					->file(substr($src, 2))
					->getPath();
			} else if (strpos($src, 'edde://') !== false) {
				return $this->assetsDirectory->file(str_replace('edde://', '', $src))
					->getPath();
			}
			return $src;
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 * @throws FileException
		 * @throws MacroException
		 */
		public function macro(INode $macro, ICompiler $compiler) {
			if ($macro->hasAttribute('standalone')) {
				$this->write($compiler, sprintf('$this->styleSheetList->addResource(new %s(%s::create(%s)));', Resource::class, Url::class, $this->attribute($macro, $compiler, 'src', false)), 5);
				return;
			}
			$this->write($compiler, sprintf('$this->styleSheetCompiler->addFile(%s);', $this->attribute($macro, $compiler, 'src', false)), 5);
		}
	}
