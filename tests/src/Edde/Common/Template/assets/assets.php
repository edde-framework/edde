<?php
	declare(strict_types=1);

	class SomeTemplateContext {
		public function iterateOverSomeInclude() {
			return [
				1,
				2,
				3,
			];
		}

		public function numOfIterations(int $a = 1, $what = 'what') {
			return array_fill(0, $a, $what);
		}

		public function thisIsTrue() {
			return true;
		}
	}
