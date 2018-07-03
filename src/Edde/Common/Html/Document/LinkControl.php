<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Document;

	use Edde\Common\Html\AbstractHtmlControl;

	/**
	 * Link html tag control.
	 */
	class LinkControl extends AbstractHtmlControl {
		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 */
		public function getTag(): string {
			return 'link';
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 */
		public function isPair(): bool {
			return false;
		}
	}
