<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Node\NodeException;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;

	/**
	 * Snippet is piece of template which can be called without any other dependencies.
	 */
	class SnippetMacro extends AbstractHtmlMacro {
		/**
		 * My brother-in-law was typing on his new laptop. His ten-year-old daughter sneaked up behind him. Then she turned and ran into the kitchen, squealing to the rest of the family, "I know Daddy's password! I know Daddy's password!"
		 *
		 * "What is it? I asked her eagerly.
		 *
		 * Proudly she replied, "Asterisk, asterisk, asterisk, asterisk, asterisk!"
		 */
		public function __construct() {
			parent::__construct('snippet');
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 * @throws NodeException
		 */
		public function inline(INode $macro, ICompiler $compiler) {
			return $this->switchlude($macro, 'id');
		}

		/**
		 * @inheritdoc
		 * @throws MacroException
		 */
		public function compile(INode $macro, ICompiler $compiler) {
			$macro->setMeta('snippet', true);
			$compiler->block($this->attribute($macro, $compiler, 'id'), $macro);
			parent::compile($macro, $compiler);
		}
	}
