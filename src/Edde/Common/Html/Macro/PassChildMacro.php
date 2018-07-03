<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Crypt\LazyCryptEngineTrait;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Reflection\ReflectionUtils;
	use Edde\Common\Strings\StringUtils;

	/**
	 * Shortcut for multiple nodes injection into some method (variable pass makes no sense).
	 */
	class PassChildMacro extends AbstractHtmlMacro {
		use LazyCryptEngineTrait;

		/**
		 * Profanity is the one language that all programmers know best.
		 */
		public function __construct() {
			parent::__construct('pass-child');
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 */
		public function inline(INode $macro, ICompiler $compiler) {
			return $this->insert($macro, 'target');
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 * @throws MacroException
		 */
		public function macro(INode $macro, ICompiler $compiler) {
			$target = $this->attribute($macro, $compiler, 'target', false);
			$func = substr($target, -2) === '()';
			$target = str_replace('()', '', $target);
			$type = $target[0];
			$target = StringUtils::toCamelHump(substr($target, 1));
			$write = sprintf('%s->%s($control);', $this->reference($macro, $type), $target);
			if ($func === false) {
				$write = sprintf('%s::setProperty(%s, %s, $control);', ReflectionUtils::class, $this->reference($macro, $type), var_export($target, true));
			}
			foreach ($macro->getNodeList() as $node) {
				if ($node->getMeta('snippet', false)) {
					continue;
				}
				$compiler->macro($node);
				/** @noinspection DisconnectedForeachInstructionInspection */
				$this->write($compiler, $write, 5);
			}
		}
	}
