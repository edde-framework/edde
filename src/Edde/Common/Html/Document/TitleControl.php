<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Document;

	use Edde\Common\Html\AbstractHtmlControl;

	/**
	 * Html title control.
	 */
	class TitleControl extends AbstractHtmlControl {
		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 */
		public function getTag(): string {
			return 'title';
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 */
		public function isPair(): bool {
			return true;
		}

		/**
		 * set a html title
		 *
		 * @param string $title
		 *
		 * @return $this
		 */
		public function setTitle(string $title) {
			$this->use();
			$this->node->setValue($title);
			return $this;
		}
	}
