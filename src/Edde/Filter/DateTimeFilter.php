<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	class DateTimeFilter extends AbstractFilter {
		/**
		 * @inheritdoc
		 */
		public function filter($value, array $options = []) {
			return $value ? new \DateTime($value) : null;
		}
	}