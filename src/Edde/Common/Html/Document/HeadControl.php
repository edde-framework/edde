<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Document;

	use Edde\Common\Html\AbstractHtmlControl;

	/**
	 * Head html tag control.
	 */
	class HeadControl extends AbstractHtmlControl {
		/**
		 * @var TitleControl
		 */
		protected $title;

		/**
		 * set the title of this head control
		 *
		 * @param string $title
		 */
		public function setTitle($title) {
			$this->use();
			$this->title->setTitle($title);
		}

		/**
		 * add the given javascript file to this head; the src should be accessible from a client
		 *
		 * @param string $src
		 *
		 * @return $this
		 */
		public function addJavaScript(string $src) {
			$this->use();
			$this->addControl($this->createControl(JavaScriptControl::class)
				->setSrc($src));
			return $this;
		}

		/**
		 * add the given stylesheet; the file should be accessible from a client
		 *
		 * @param string $href
		 *
		 * @return $this
		 */
		public function addStyleSheet(string $href) {
			$this->use();
			$this->addControl($this->createControl(StyleSheetControl::class)
				->setHref($href));
			return $this;
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 */
		public function getTag(): string {
			return 'head';
		}

		/**
		 * @inheritdoc
		 */
		protected function prepare() {
			parent::prepare();
			$this->addControl($this->createControl(MetaControl::class)
				->setAttribute('charset', 'utf-8'));
			$this->addControl($this->title = $this->createControl(TitleControl::class));
		}
	}
