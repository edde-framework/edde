<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Reflection\ReflectionUtils;
	use Edde\Common\Strings\StringUtils;

	/**
	 * Inline pass macro will generate pass macros which will execute "final" pass.
	 */
	class PassMacro extends AbstractHtmlMacro {
		/**
		 * Hardware: "A product that if you play with it long enough, breaks."
		 *
		 * Software: "A product that if you play with it long enough, it works."
		 */
		public function __construct() {
			parent::__construct('pass');
		}

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
			$this->write($compiler, $write, 5);
			parent::macro($macro, $compiler);
		}
	}
