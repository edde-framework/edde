<?php
	declare(strict_types = 1);

	use Edde\Common\Converter\AbstractConverter;

	class DummyConverter extends AbstractConverter {
		public function convert($convert, string $source, string $target, string $mime) {
			return $convert;
		}
	}

	class CleverConverter extends AbstractConverter {
		public function convert($convert, string $source, string $target, string $mime) {
			return $convert;
		}
	}
