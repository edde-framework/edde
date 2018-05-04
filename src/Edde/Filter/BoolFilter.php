<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use stdClass;

	/**
	 * Both sides (input/output) ensures boolean.
	 */
	class BoolFilter extends AbstractFilter {
		/** @inheritdoc */
		public function input($value, ?stdClass $options = null) {
			return filter_var($value, FILTER_VALIDATE_BOOLEAN);
		}

		/** @inheritdoc */
		public function output($value, ?stdClass $options = null) {
			return filter_var($value, FILTER_VALIDATE_BOOLEAN);
		}
	}
