<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;

	/**
	 * Translator support macro.
	 */
	class TranslatorMacro extends AbstractHtmlMacro {
		/**
		 * While inspecting their honeymoon suite, the bride discovers a little box attached to the bed.
		 * "What's this for?" she asks her husband.
		 * "If you put a quarter in," he says, reaching into his pocket, "the bed starts vibrating."
		 * "Save your money," she says. "When you're a quarter in, I start vibrating."
		 */
		public function __construct() {
			parent::__construct('translator');
		}

		/**
		 * @inheritdoc
		 */
		public function inline(INode $macro, ICompiler $compiler) {
			return $this->switchlude($macro, 'scope');
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 * @throws MacroException
		 */
		public function macro(INode $macro, ICompiler $compiler) {
			$this->write($compiler, sprintf('$this->translator->pushScope(%s);', var_export($this->attribute($macro, $compiler, 'scope', false), true)), 5);
			parent::macro($macro, $compiler);
			$this->write($compiler, '$this->translator->popScope();', 5);
		}
	}
