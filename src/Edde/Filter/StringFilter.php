<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	class StringFilter extends AbstractFilter {
		/** @inheritdoc */
		public function input($value, ?array $options = null) {
			return (string)$value;
		}

		/** @inheritdoc */
		public function output($value, ?array $options = null) {
			return (string)$value;
		}
	}
