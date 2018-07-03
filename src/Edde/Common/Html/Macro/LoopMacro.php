<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Crypt\LazyCryptEngineTrait;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\IHelper;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Strings\StringException;
	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Template\HelperSet;

	/**
	 * Macro for loop support.
	 */
	class LoopMacro extends AbstractHtmlMacro implements IHelper {
		use LazyCryptEngineTrait;

		/**
		 * You never finish a program, you just stop working on it.
		 */
		public function __construct() {
			parent::__construct('loop');
		}

		/**
		 * @inheritdoc
		 */
		public function inline(INode $macro, ICompiler $compiler) {
			return $this->switchlude($macro, 'src');
		}

		/**
		 * @inheritdoc
		 * @throws MacroException
		 * @throws StringException
		 */
		public function helper(INode $macro, ICompiler $compiler, $value, ...$parameterList) {
			/** @var $stack \SplStack */
			$stack = $compiler->getVariable(static::class);
			if ($value === null || $stack === null || $stack->isEmpty()) {
				return null;
			} else if ($value === '$:') {
				list(, $value) = $stack->top();
				return '$value_' . $value;
			} else if ($value === '$#') {
				list($key,) = $stack->top();
				return '$key_' . $key;
			} else if ($match = StringUtils::match($value, '~\$(?<type>:|#)(?<jump>\$|\.|\d+)?(\->(?<call>[a-z0-9-]+\(\)))?~', true, true)) {
				$jump = $match['jump'] ?? 0;
				$type = $match['type'];
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
				list($key, $value) = $loop;
				if ($type === '#') {
					return '$key_' . $key;
				}
				return '$value_' . $value . (isset($match['call']) ? '->' . StringUtils::toCamelHump($match['call']) : '');
			}
			return null;
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 * @throws MacroException
		 */
		public function macro(INode $macro, ICompiler $compiler) {
			/** @var $stack \SplStack */
			$stack = $compiler->getVariable(static::class, new \SplStack());
			$loop = [
				$key = str_replace('-', '_', $this->cryptEngine->guid()),
				$value = str_replace('-', '_', $this->cryptEngine->guid()),
			];
			$this->write($compiler, '$control = $stack->top();', 5);
			$src = $this->attribute($macro, $compiler, 'src', false);
			$this->write($compiler, sprintf('foreach(%s as $key_%s => $value_%s) {', ($helper = $compiler->helper($macro, $src)) ? $helper : $this->loop($macro, $compiler, $src), $key, $value), 5);
			$stack->push($loop);
			parent::macro($macro, $compiler);
			$stack->pop();
			$this->write($compiler, '}', 5);
		}

		/**
		 * @param INode $macro
		 * @param ICompiler $compiler
		 * @param string $src
		 *
		 * @return mixed|string
		 * @throws MacroException
		 */
		protected function loop(INode $macro, ICompiler $compiler, string $src) {
			$type = $src[0];
			if (isset(self::$reference[$type])) {
				return sprintf('%s->%s', $this->reference($macro, $type), StringUtils::toCamelHump(substr($src, 1)));
			} else if ($src === '$:') {
				list(, $value) = $compiler->getVariable(static::class)
					->top();
				return '$value_' . $value;
			}
			return var_export($src, true);
		}

		protected function prepare() {
			parent::prepare();
			$this->helperSet = new HelperSet();
			$this->helperSet->registerHelper($this);
		}
	}
