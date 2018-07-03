<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Html\HtmlException;
	use Edde\Api\Html\IHtmlControl;

	/**
	 * Simple invisible control used for controls created via ajax.
	 */
	class PlaceholderControl extends AbstractHtmlControl {
		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 * @throws HtmlException
		 */
		public function setTag(string $tag, bool $pair = true): IHtmlControl {
			throw new HtmlException(sprintf('Cannot set tag [%s] to a placeholder control.', $tag));
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 */
		public function isPair(): bool {
			return true;
		}

		/**
		 * @inheritdoc
		 */
		protected function prepare() {
			parent::prepare();
			parent::setTag('div');
			$this->addClass('edde-placeholder');
		}
	}
