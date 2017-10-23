<?php
	namespace Edde\Common\Filter;

	/**
	 * Output will be boolean.
	 */
		class ToBoolFilter extends AbstractFilter {
			public function filter($value, array $options = []) {
				return filter_var($value, FILTER_VALIDATE_BOOLEAN);
			}
		}
