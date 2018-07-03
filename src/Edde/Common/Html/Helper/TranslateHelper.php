<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Helper;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Translator\LazyTranslatorTrait;
	use Edde\Common\Template\AbstractHelper;

	/**
	 * Translate helper support.
	 */
	class TranslateHelper extends AbstractHelper {
		use LazyTranslatorTrait;

		/**
		 * @inheritdoc
		 */
		public function helper(INode $macro, ICompiler $compiler, $value, ...$parameterList) {
			if ($value !== null && strlen($value) > 10 && ($index = strrpos($value, '|translate', -10)) !== false) {
				return sprintf('$this->translator->translate(%s)', var_export(substr($value, 0, $index), true));
			}
			return null;
		}
	}
