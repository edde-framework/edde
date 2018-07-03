<?php
	declare(strict_types=1);

	namespace Edde\Ext\Control;

	use Edde\Common\Control\AbstractControl;
	use Edde\Common\Strings\StringUtils;
	use Edde\Ext\Template\TemplateTrait;

	abstract class AbstractTemplateControl extends AbstractControl {
		use TemplateTrait;

		/**
		 * return current context name for this control; by default is based on current called action
		 *
		 * @return string
		 */
		public function getContextName() {
			return implode('\\', array_slice(explode('\\', static::class), -2, 1)) . '\\' . str_replace('Action', '', StringUtils::toCamelCase((string)$this->routerService->createRequest()->getMeta('::method'))) . 'TemplateContext';
		}
	}
