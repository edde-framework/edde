<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use DateTime;

	class DateTimeFilter extends AbstractFilter {
		/** @inheritdoc */
		public function filter($value, array $options = []) {
			if ($value instanceof DateTime) {
				return $value;
			} else if ($value) {
				return new DateTime($value);
			}
			return null;
		}
	}
