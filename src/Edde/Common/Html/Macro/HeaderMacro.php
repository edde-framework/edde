<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Common\Html\HeaderControl;

	/**
	 * Classic html header macro (h1, h2, ...).
	 */
	class HeaderMacro extends HtmlMacro {
		/**
		 * Top Ten Reasons "Men Prefer Computers to Women"
		 *
		 * 10.) Computers have "help" text when you get confused.
		 *
		 * 9.) When you get tired of a computer, you can shut it off.
		 *
		 * 8.) Your friends will always tell you, you could have a better one.
		 *
		 * 7.) Booting a computer is not a punishable offense.
		 *
		 * 6.) You can get upgrades without going to a plastic surgeon.
		 *
		 * 5.) "Cheap" and "Fast" are good attributes in a computer.
		 *
		 * 4.) Nobody stares if you finger a computer in your office.
		 *
		 * 3.) A computer doesn't get mad if you play with someone else's computer.
		 *
		 * 2.) It only takes a couple of seconds to turn one on. AND THE NUMBER 1 REASON..........
		 *
		 * 1.) Computers will take a 3-1/2 floppy & be happy about it.
		 *
		 * BACK TO JOKES PAGE  GET THE JOKE EXPLANATION
		 *
		 * @param string $header
		 */
		public function __construct(string $header) {
			parent::__construct($header, HeaderControl::class);
		}

		/**
		 * @inheritdoc
		 */
		protected function onControl(INode $macro, ICompiler $compiler) {
			$this->write($compiler, sprintf('$control->setTag(%s);', var_export($this->getName(), true)), 5);
		}
	}
