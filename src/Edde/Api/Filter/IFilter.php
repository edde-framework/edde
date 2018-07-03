<?php
	declare(strict_types=1);

	namespace Edde\Api\Filter;

	use Edde\Api\Config\IConfigurable;

	interface IFilter extends IConfigurable {
		/**
		 * filter input to value to a output value; if filtering (conversion) cannot be done, exception should be thrown
		 *
		 * @param mixed $value
		 * @param array $parameterList if a filter needs some more parameters...
		 *
		 * @return mixed
		 */
		public function filter($value, ...$parameterList);
	}
