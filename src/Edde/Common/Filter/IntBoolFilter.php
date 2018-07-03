<?php
	declare(strict_types = 1);

	namespace Edde\Common\Filter;

	class IntBoolFilter extends AbstractFilter {
		public function filter($value, ...$parameterList) {
			return (int)filter_var($value, FILTER_VALIDATE_BOOLEAN);
		}
	}
