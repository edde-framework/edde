<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Helper;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Html\Macro\AbstractHtmlMacro;
	use Edde\Common\Strings\StringException;
	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Template\AbstractHelper;

	/**
	 * This helper will translate "standard" call format into methods reffering to root or current control; it also supports container call reference.
	 */
	class MethodHelper extends AbstractHelper {
		/**
		 * @inheritdoc
		 * @throws StringException
		 * @throws MacroException
		 */
		public function helper(INode $macro, ICompiler $compiler, $value, ...$parameterList) {
			if ($value === null) {
				return null;
			}
			/**
			 * intentionall assigment
			 */
			if ($match = StringUtils::match($value, '~^(?<type>\.|@|#|:)(?<method>[a-z0-9-]+)\(\)$~', true, true)) {
				return sprintf('%s->%s($stack->top())', AbstractHtmlMacro::reference($macro, $match['type']), StringUtils::toCamelHump($match['method']));
			} else if ($match = StringUtils::match($value, '~^(?<class>(\\\\[a-zA-Z0-9_]+)+)::(?<method>[a-zA-Z_]+)\(\)$~', true, true)) {
				return sprintf('$this->container->create(%s)->%s($stack->top())', var_export($match['class'], true), $match['method']);
			}
			return null;
		}
	}
