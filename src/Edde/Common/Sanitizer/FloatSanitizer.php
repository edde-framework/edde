<?php
	declare(strict_types=1);
	namespace Edde\Common\Sanitizer;

		class FloatSanitizer extends AbstractSanitizer {
			/**
			 * @inheritdoc
			 */
			public function sanitize($value, array $options = []) {
				return (float)$value;
			}
		}
