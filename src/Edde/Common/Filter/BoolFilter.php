<?php
	namespace Edde\Common\Filter;

	/**
	 * Output will be boolean.
	 */
		class BoolFilter extends AbstractFilter {
			public function filter($value, array $options = []) {
				return filter_var($value, FILTER_VALIDATE_BOOLEAN);
			}
		}
