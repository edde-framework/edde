<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use stdClass;
	use function filter_var;
	use const FILTER_VALIDATE_BOOLEAN;

	class BoolIntFilter extends AbstractFilter {
		/** @inheritdoc */
		public function input($value, ?stdClass $options = null) {
			return (int)$this->output($value);
		}

		/** @inheritdoc */
		public function output($value, ?stdClass $options = null) {
			return filter_var($value, FILTER_VALIDATE_BOOLEAN);
		}
	}
