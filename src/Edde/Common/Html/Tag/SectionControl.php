<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Tag;

	use Edde\Api\Html\HtmlException;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Common\Html\AbstractHtmlControl;

	/**
	 * Section html tag control.
	 */
	class SectionControl extends AbstractHtmlControl {
		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 * @throws HtmlException
		 */
		public function setTag(string $tag, bool $pair = true): IHtmlControl {
			throw new HtmlException(sprintf('Cannot set tag [%s] to a [%s] control.', $tag, static::class));
		}

		/**
		 * @inheritdoc
		 */
		protected function prepare() {
			parent::prepare();
			parent::setTag('section', true);
		}
	}
