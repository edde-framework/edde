<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use DateTime;
	use stdClass;

	class DateTimeFilter extends AbstractFilter {
		/** @inheritdoc */
		public function input($value, ?stdClass $options = null) {
			if ($value && $value instanceof DateTime === false) {
				$value = new DateTime($value);
			}
			return $value ? $value->format('Y-m-d H:i:s.u') : null;
		}

		/** @inheritdoc */
		public function output($value, ?stdClass $options = null) {
			if ($value instanceof DateTime) {
				return $value;
			} else if ($value) {
				return new DateTime($value);
			}
			return null;
		}
	}
