<?php
	namespace Edde\Common\Filter;

		class DateTimeFilter extends AbstractFilter {
			/**
			 * @inheritdoc
			 */
			public function filter($value, array $options = []) {
				return new \DateTime($value);
			}
		}