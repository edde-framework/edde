<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use function filter_var;
	use const FILTER_VALIDATE_BOOLEAN;

	class BoolIntFilter extends AbstractFilter {
		/** @inheritdoc */
		public function input($value, ?array $options = null) {
			return filter_var($value, FILTER_VALIDATE_BOOLEAN);
		}

		/** @inheritdoc */
		public function output($value, ?array $options = null) {
			return (int)$this->input($value);
		}
	}
