<?php
	declare(strict_types=1);
	namespace Edde\Filter;

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
