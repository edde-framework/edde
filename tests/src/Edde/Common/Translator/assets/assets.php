<?php
	declare(strict_types = 1);

	namespace Foo\Bar;

	use Edde\Common\Translator\AbstractDictionary;

	class EmptyDictionary extends AbstractDictionary {
		/**
		 * @inheritdoc
		 */
		public function translate(string $id, string $language) {
			return null;
		}

		protected function prepare() {
		}
	}

	class DummyDictionary extends AbstractDictionary {
		/**
		 * @inheritdoc
		 */
		public function translate(string $id, string $language) {
			return $id . '.' . $language;
		}

		protected function prepare() {
		}
	}
