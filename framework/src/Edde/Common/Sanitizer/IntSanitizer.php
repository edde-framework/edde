<?php
	declare(strict_types=1);
	namespace Edde\Common\Sanitizer;

	class IntSanitizer extends AbstractSanitizer {
		/** @inheritdoc */
		public function sanitize($value, array $options = []) {
			return (int)$value;
		}
	}
