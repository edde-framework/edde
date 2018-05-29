<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use stdClass;
	use const FILTER_VALIDATE_INT;

	class IntFilter extends AbstractFilter {
		/** @inheritdoc */
		public function input($value, ?stdClass $options = null) {
			return filter_var($value, FILTER_VALIDATE_INT);
		}

		/** @inheritdoc */
		public function output($value, ?stdClass $options = null) {
			return filter_var($value, FILTER_VALIDATE_INT);
		}
	}
