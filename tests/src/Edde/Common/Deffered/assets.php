<?php
	declare(strict_types = 1);

	namespace Edde\Common\Deffered;

	use Edde\Common\AbstractObject;

	/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
	class UsableObject extends AbstractDeffered {
		public $prepared = false;

		public function takeAction() {
			$this->use();
		}

		/**
		 * @inheritdoc
		 */
		protected function prepare() {
			$this->prepared = true;
		}
	}

	/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
	class UsableTraitedObject extends AbstractObject {
		use DefferedTrait;

		public $prepared = false;

		public function takeAction() {
			$this->use();
		}

		protected function prepare() {
			$this->prepared = true;
		}
	}
