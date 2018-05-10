<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use stdClass;

	class StringFilter extends AbstractFilter {
		/** @inheritdoc */
		public function input($value, ?stdClass $options = null) {
			return (string)$value;
		}

		/** @inheritdoc */
		public function output($value, ?stdClass $options = null) {
			return (string)$value;
		}
	}
