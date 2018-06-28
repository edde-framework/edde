<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	interface IFilter {
		/**
		 * filter value for an input (in the given context, could be "world" to PHP; for example json_decode)
		 *
		 * @param mixed      $value
		 * @param null|array $options
		 *
		 * @return mixed
		 *
		 * @throws FilterException
		 */
		public function input($value, ?array $options = null);

		/**
		 * filter value for an output (in the given context, could be PHP to "world"; for example json_encode)
		 *
		 * @param mixed      $value
		 * @param null|array $options
		 *
		 * @return mixed
		 *
		 * @throws FilterException
		 */
		public function output($value, ?array $options = null);
	}
