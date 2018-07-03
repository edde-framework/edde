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
	 * Switch support.
	 */
	class SwitchMacro extends AbstractHtmlMacro {
		use LazyCryptEngineTrait;

		/**
		 * A programmer enters an elevator, wanting to go to the 12th floor.
		 *
		 * So, he pushes 1, then he pushes 2, and starts looking for the Enter...
		 */
		public function __construct() {
			parent::__construct('switch');
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 * @throws MacroException
		 */
		public function inline(INode $macro, ICompiler $compiler) {
			return $this->switchlude($macro, 'src');
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 * @throws MacroException
		 */
		public function macro(INode $macro, ICompiler $compiler) {
			$stack = $compiler->getVariable(static::class, $stack = new \SplStack());
			$this->write($compiler, sprintf('$switch_%s = %s;', $switch = str_replace('-', '_', $this->cryptEngine->guid()), $this->generate($macro, $this->attribute($macro, $compiler, 'src', false))), 5);
			$stack->push($switch);
			parent::macro($macro, $compiler);
			$stack->pop();
		}

		/**
		 * @param INode $macro
		 * @param string $src
		 *
		 * @return string
		 * @throws MacroException
		 */
		protected function generate(INode $macro, string $src): string {
			$func = substr($src, -2) === '()';
			$src = str_replace('()', '', $src);
			$type = $src[0];
			$src = StringUtils::toCamelHump(substr($src, 1));
			if ($func) {
				return sprintf('%s->%s()', $this->reference($macro, $type), $src);
			}
			return sprintf('%s::getProperty(%s, %s)', ReflectionUtils::class, $this->reference($macro, $type), var_export($src, true));
		}
	}
