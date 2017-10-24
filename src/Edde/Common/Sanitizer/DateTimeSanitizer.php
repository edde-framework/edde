<?php
	namespace Edde\Common\Sanitizer;

		class DateTimeSanitizer extends AbstractSanitizer {
			/**
			 * @inheritdoc
			 *
			 * @param \DateTime $value
			 */
			public function sanitize($value, array $options = []) {
				return $value->format('Y-m-d H:i:s');
			}
		}
