<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Document;

	class StyleSheetControl extends LinkControl {
		public function setHref($href) {
			$this->setAttribute('href', $href);
			return $this;
		}

		protected function prepare() {
			parent::prepare();
			$this->setAttribute('rel', 'stylesheet')
				->setAttribute('media', 'all');
		}
	}
