<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use stdClass;
	use const FILTER_VALIDATE_FLOAT;

	class FloatFilter extends AbstractFilter {
		/** @inheritdoc */
		public function input($value, ?stdClass $options = null) {
			return filter_var($value, FILTER_VALIDATE_FLOAT);
		}

		/** @inheritdoc */
		public function output($value, ?stdClass $options = null) {
			return filter_var($value, FILTER_VALIDATE_FLOAT);
		}
	}
