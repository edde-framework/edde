<?php
	declare(strict_types = 1);

	namespace Edde\Common\Filter;

	class BoolFilter extends AbstractFilter {
		public function filter($value, ...$parameterList) {
			return filter_var($value, FILTER_VALIDATE_BOOLEAN);
		}
	}
