<?php
	namespace Edde\Common\Sanitizer;

		class FloatSanitizer extends AbstractSanitizer {
			/**
			 * @inheritdoc
			 */
			public function sanitize($value, array $options = []) {
				return (float)$value;
			}
		}
