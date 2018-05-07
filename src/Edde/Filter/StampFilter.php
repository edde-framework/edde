<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use DateTime;
	use stdClass;

	class StampFilter extends AbstractFilter {
		/** @inheritdoc */
		public function input($value, ?stdClass $options = null) {
			if (empty($value) === false) {
				return $value;
			}
			return new DateTime();
		}

		/** @inheritdoc */
		public function output($value, ?stdClass $options = null) {
			return $value;
		}
	}
