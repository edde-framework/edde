<?php
	declare(strict_types = 1);

	use Edde\Common\Html\AbstractHtmlControl;
	use Edde\Common\Html\Tag\DivControl;
	use Edde\Common\Html\TemplateTrait;
	use Edde\Common\Html\ViewControl;

	/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
	class MyLittleCuteView extends ViewControl {
		/**
		 * @var DivControl
		 */
		public $templateSnippet;

		public function myDivSnippet(DivControl $divControl) {
			$divControl->setText('foo');
			$divControl->dirty();
		}

		public function myDummySnippet(DivControl $divControl) {
		}

		public function templateSnippet(DivControl $divControl) {
			$divControl->dirty();
		}
	}

	/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
	class SomeTemplatedControl extends AbstractHtmlControl {
		use TemplateTrait;

		protected function prepare() {
			parent::prepare();
			$this->cache();
			$this->setTag('foo');
		}
	}
