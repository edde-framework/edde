<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Tag;

	use Edde\Api\Html\HtmlException;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Common\Html\AbstractHtmlControl;

	/**
	 * Table body html tag.
	 */
	class TableBodyControl extends AbstractHtmlControl {
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
			parent::setTag('tbody', true);
		}
	}
