<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;

	class FillMacro extends AbstractHtmlMacro {
		public function __construct() {
			parent::__construct('fill');
		}

		/**
		 * @inheritdoc
		 */
		public function inline(INode $macro, ICompiler $compiler) {
			$macro->setAttribute('data-fill', $this->extract($macro, 't:' . $this->getName()));
		}
	}
