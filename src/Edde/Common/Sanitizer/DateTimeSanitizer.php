<?php
	declare(strict_types=1);
	namespace Edde\Common\Sanitizer;

		class DateTimeSanitizer extends AbstractSanitizer {
			/**
			 * @inheritdoc
			 *
			 * @param \DateTime $value
			 */
			public function sanitize($value, array $options = []) {
				return $value ? $value->format($options['format'] ?? 'Y-m-d H:i:s.u') : null;
			}
		}
