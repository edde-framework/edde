<?php
	declare(strict_types=1);
	namespace Edde\Common\Filter;

	use const FILTER_VALIDATE_INT;

	class IntFilter extends AbstractFilter {
		/**
		 * @inheritdoc
		 */
		public function filter($value, array $options = []) {
			return filter_var($value, FILTER_VALIDATE_INT);
		}
	}
