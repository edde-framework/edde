<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;

	class TitleMacro extends AbstractHtmlMacro {
		public function __construct() {
			parent::__construct('title');
		}

		public function macro(INode $macro, ICompiler $compiler) {
			$helper = $compiler->helper($macro, $value = $this->extract($macro, 'value'));
			$this->write($compiler, sprintf("%s->setAttribute('title', %s);", self::reference($macro, ':'), $helper ?: var_export($value, true)), 5);
		}
	}
