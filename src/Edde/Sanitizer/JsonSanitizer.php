<?php
	declare(strict_types=1);
	namespace Edde\Sanitizer;

	use function json_encode;

	class JsonSanitizer extends AbstractSanitizer {
		/** @inheritdoc */
		public function sanitize($value, array $options = []) {
			return json_encode($value);
		}
	}
