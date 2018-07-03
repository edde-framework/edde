<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\File\LazyRootDirectoryTrait;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;

	/**
	 * Load macro adds support for loading templates on demand.
	 */
	class LoadMacro extends AbstractHtmlMacro {
		use LazyRootDirectoryTrait;

		/**
		 * An artist, a lawyer, and a computer scientist are discussing the merits of a extra-marital affair over coffee one afternoon.
		 *
		 * The artist tells of the passion, the thrill which comes with the risk of being discovered. The lawyer warns of the difficulties. It can lead to guilt, divorce, bankruptcy. Not worth it. Too many problems.
		 *
		 * The computer scientist says "My affair is the best thing that's ever happened to me. My wife thinks I'm with my lover. My lover thinks I'm home with my wife, and I can spend all night on the computer!"
		 */
		public function __construct() {
			parent::__construct('load');
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 * @throws MacroException
		 */
		public function macro(INode $macro, ICompiler $compiler) {
			$this->write($compiler, sprintf('$this->embedd($template = self::template($this->templateManager->template(%s), $this->container));', ($helper = $compiler->helper($macro, $src = $this->attribute($macro, $compiler, 'src', false))) ? $helper : $this->load($src, $compiler)), 5);
			$this->write($compiler, '$template->snippet($stack->top());', 5);
		}

		/**
		 * compute path from macro's source attribute
		 *
		 * @param string $src
		 * @param ICompiler $compiler
		 *
		 * @return string
		 */
		protected function load(string $src, ICompiler $compiler) {
			if (strpos($src, '/') === 0) {
				$src = $this->rootDirectory->filename(substr($src, 1));
			} else if (strpos($src, './') === 0) {
				$src = $compiler->getSource()
					->getDirectory()
					->filename(substr($src, 2));
			}
			return var_export($src, true);
		}
	}
