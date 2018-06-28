<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use DateTime;

	class StampFilter extends AbstractFilter {
		/** @inheritdoc */
		public function input($value, ?array $options = null) {
			if (empty($value) === false) {
				return $value;
			}
			return new DateTime();
		}

		/** @inheritdoc */
		public function output($value, ?array $options = null) {
			return $value;
		}
	}
