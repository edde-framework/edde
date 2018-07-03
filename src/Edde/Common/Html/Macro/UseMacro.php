<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;

	/**
	 * Use macro can be used as a block reference "on demand" (similar to t:include macro).
	 */
	class UseMacro extends AbstractHtmlMacro {
		/**
		 * If God had intended Man to program, we would be born with USB ports.
		 */
		public function __construct() {
			parent::__construct('use');
		}

		/**
		 * @inheritdoc
		 */
		public function inline(INode $macro, ICompiler $compiler) {
			return $this->insert($macro, 'src');
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 * @throws MacroException
		 */
		public function macro(INode $macro, ICompiler $compiler) {
			$this->write($compiler, sprintf('$this->block($stack->top(), %s);', ($helper = $compiler->helper($macro, $src = $this->attribute($macro, $compiler, 'src', false))) ? $helper : var_export($src, true)), 5);
		}
	}
