<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	/**
	 * Output will be boolean.
	 */
	class BoolFilter extends AbstractFilter {
		public function filter($value, array $options = []) {
			return filter_var($value, FILTER_VALIDATE_BOOLEAN);
		}
	}
