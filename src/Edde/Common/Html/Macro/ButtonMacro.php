<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Html\Tag\ButtonControl;

	/**
	 * Special case of Control macro for button control support.
	 */
	class ButtonMacro extends HtmlMacro {
		/**
		 * BASIC is to computer programming as "qwerty" is to typing.
		 */
		public function __construct() {
			parent::__construct('button', ButtonControl::class);
		}

		/**
		 * @inheritdoc
		 * @throws MacroException
		 */
		protected function onControl(INode $macro, ICompiler $compiler) {
			if (($action = $this->extract($macro, 'action')) !== null) {
				$this->write($compiler, sprintf('$control->setAction(%s);', $this->action($macro, $compiler, $action)), 5);
			}
		}

		/**
		 * @param INode $macro
		 * @param ICompiler $compiler
		 * @param string $action
		 *
		 * @return mixed|string
		 * @throws MacroException
		 */
		protected function action(INode $macro, ICompiler $compiler, string $action) {
			if (substr($action, -2) === '()') {
				$type = $action[0];
				$action = var_export(str_replace('()', '', substr($action, 1)), true);
				return sprintf('[%s, %s]', $this->reference($macro, $type), $action);
			} else if ($helper = $compiler->helper($macro, $action)) {
				return $helper;
			}
			return var_export($action, true);
		}
	}
