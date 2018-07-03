<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;

	/**
	 * Switch-case macro.
	 */
	class CaseMacro extends AbstractHtmlMacro {
		/**
		 * A lead hardware engineer, a lead software engineer, and their Project Manager are taking a walk outdoors during their lunch break when they come upon an old brass lamp. They pick it up and dust it off. Poof -- out pops a genie.
		 * "Thank you for releasing me from my lamp prison. I can grant you 3 wishes. Since there are 3 of you I will grant one wish to each of you."
		 *
		 * The hardware engineer thinks a moment and says, "I'd like to be sailing a yacht across the Pacific, racing before the wind, with an all-girl crew."
		 *
		 * "It is done", said the Genie, and poof, the hardware engineer disappears.
		 *
		 * The software engineer thinks a moment and says, "I'd like to be riding my Harley with a gang of beautiful women throughout the American Southwest."
		 *
		 * "It is done", said the Genie, and poof, the software engineer disappears.
		 *
		 * The Project Manager looks at where the other two had been standing and rubs his chin in thought. Then he tells the Genie, "I'd like those two back in the office after lunch."
		 */
		public function __construct() {
			parent::__construct('case');
		}

		/**
		 * @inheritdoc
		 */
		public function inline(INode $macro, ICompiler $compiler) {
			return $this->switchlude($macro, 'name');
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 * @throws MacroException
		 */
		public function macro(INode $macro, ICompiler $compiler) {
			/** @var $stack \SplStack */
			if (($stack = $compiler->getVariable(SwitchMacro::class)) === null || $stack->isEmpty()) {
				throw new MacroException(sprintf('Shit has happend: macro [%s] has no parent switch!', $macro->getPath()));
			}
			$switch = $stack->top();
			$this->write($compiler, sprintf('if($switch_%s === %s) {', $switch, var_export($this->attribute($macro, $compiler, 'name', false), true)), 5);
			parent::macro($macro, $compiler);
			$this->write($compiler, '}', 5);
		}
	}
