<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;

	class AttrMacro extends AbstractHtmlMacro {
		public function __construct() {
			parent::__construct('attr');
		}

		public function macro(INode $macro, ICompiler $compiler) {
			$reference = $this->attribute($macro, $compiler, 'name');
			$method = strpos($reference, '[]') !== false ? 'addAttribute' : 'setAttribute';
			$this->write($compiler, sprintf('%s->%s(%s, %s);', self::reference($macro, $reference[0]), $method, var_export(str_replace('[]', '', substr($reference, 1)), true), var_export($this->attribute($macro, $compiler, 'value'), true)), 5);
		}
	}
