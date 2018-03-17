<?php
	declare(strict_types=1);
	namespace Edde\Application;

	use function array_merge;

	class TestContext extends AbstractContext {
		public function cascade(string $delimiter, string $name = null): array {
			return array_merge(parent::cascade($delimiter, $name), [
				'Foo' . $delimiter . 'Bar' . ($name ? $delimiter . $name : ''),
				'Bar' . $delimiter . 'Foo' . ($name ? $delimiter . $name : ''),
			]);
		}
	}
