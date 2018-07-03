<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Html\IHtmlControl;

	/**
	 * Html control without a representation.
	 */
	class ContainerControl extends AbstractHtmlControl {
		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 */
		public function render(int $indent = 0): string {
			$this->use();
			$renderList = [];
			/** @var $control IHtmlControl */
			foreach ($this->getControlList() as $control) {
				$renderList[] = $control->render(-1);
			}
			return implode('', $renderList);
		}
	}
