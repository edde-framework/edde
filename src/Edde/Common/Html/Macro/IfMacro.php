<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Crypt\LazyCryptEngineTrait;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\IHelper;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Reflection\ReflectionUtils;
	use Edde\Common\Strings\StringException;
	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Template\HelperSet;

	/**
	 * Condition macro support.
	 */
	class IfMacro extends AbstractHtmlMacro implements IHelper {
		use LazyCryptEngineTrait;

		/**
		 * MS-DOS is like the US railroad system. It's there, but people just ignore it and find other ways of getting where they want to go.
		 */
		public function __construct() {
			parent::__construct('if');
		}

		/**
		 * @inheritdoc
		 * @throws MacroException
		 * @throws StringException
		 */
		public function helper(INode $macro, ICompiler $compiler, $value, ...$parameterList) {
			/** @var $stack \SplStack */
			$stack = $compiler->getVariable(static::class);
			if ($value === null) {
				return null;
			} else if ($value === '?:') {
				return '$if_' . $stack->top();
			} else if ($match = StringUtils::match($value, '~\?:(?<jump>\$|\.|\d+)?(\->(?<call>[a-z0-9-]+\(\)))?~', true, true)) {
				$jump = $match['jump'] ?? 0;
				if ($jump === '$') {
					$jump = 1;
				} else if ($jump === '.') {
					$jump = $stack->count();
				}
				$loop = null;
				foreach ($stack as $loop) {
					if ($jump-- <= 0) {
						break;
					}
				}
				if ($loop === null) {
					throw new MacroException(sprintf('There are no loops for macro [%s].', $macro->getPath()));
				}
				return '$if_' . $loop . (isset($match['call']) ? '->' . StringUtils::toCamelHump($match['call']) : '');
			}
			return null;
		}

		/**
		 * @inheritdoc
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
			/** @var $stack \SplStack */
			$stack = $compiler->getVariable(static::class, new \SplStack());
			$if = str_replace('-', '_', $this->cryptEngine->guid());
			$this->write($compiler, sprintf('if($if_%s = %s) {', $if, $this->generate($macro, $this->attribute($macro, $compiler, 'src', false))), 5);
			$stack->push($if);
			parent::macro($macro, $compiler);
			$stack->pop();
			$this->write($compiler, '}', 5);
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

		protected function prepare() {
			parent::prepare();
			$this->helperSet = new HelperSet();
			$this->helperSet->registerHelper($this);
		}
	}
