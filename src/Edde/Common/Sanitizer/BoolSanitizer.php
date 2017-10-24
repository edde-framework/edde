<?php
	namespace Edde\Common\Sanitizer;

		class BoolSanitizer extends AbstractSanitizer {
			/**
			 * @inheritdoc
			 */
			public function sanitize($value, array $options = []) {
				return (int)$value;
			}
		}
