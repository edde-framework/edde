<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;

	/**
	 * This macro enables code execution from control "in the middle of template" - for example delegated control creation moved on the
	 * control side.
	 */
	class CallMacro extends AbstractHtmlMacro {
		/**
		 * I once got an especially helpful reply to a question I asked on Microsoft's on-line tech support service. I wrote back to thank them for a complete and concise reply, and said how much I appreciated it.
		 *
		 * The next day I had a response:
		 *
		 * "We are looking into the problem and will contact you with a solution as soon as possible."
		 */
		public function __construct() {
			parent::__construct('call');
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 * @throws MacroException
		 */
		public function macro(INode $macro, ICompiler $compiler) {
			$this->write($compiler, $this->attribute($macro, $compiler, 'method') . ';', 5);
		}
	}
