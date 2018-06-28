<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use const FILTER_VALIDATE_FLOAT;

	class FloatFilter extends AbstractFilter {
		/** @inheritdoc */
		public function input($value, ?array $options = null) {
			if (($value = filter_var($value, FILTER_VALIDATE_FLOAT)) === false) {
				return null;
			}
			return $value;
		}

		/** @inheritdoc */
		public function output($value, ?array $options = null) {
			return $this->input($value, $options);
		}
	}
