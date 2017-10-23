<?php
	namespace Edde\Api\Filter;

		use Edde\Api\Filter\Exception\FilterException;

		interface IFilter {
			/**
			 * filter the given value; if it's not possible, throw an exception
			 *
			 * @param mixed $value
			 * @param array $options
			 *
			 * @return mixed
			 *
			 * @throws FilterException
			 */
			public function filter($value, array $options = []);
		}
