<?php
	namespace Edde\Common\Filter;

		class FloatFilter extends AbstractFilter {
			/**
			 * @inheritdoc
			 */
			public function filter($value, array $options = []) {
				return filter_var($value, FILTER_VALIDATE_FLOAT);
			}
		}
