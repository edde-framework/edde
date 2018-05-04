<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use stdClass;

	interface IFilter {
		/**
		 * filter value for an input (in the given context, could be "world" to PHP; for example json_decode)
		 *
		 * @param mixed         $value
		 * @param null|stdClass $options
		 *
		 * @return mixed
		 */
		public function input($value, ?stdClass $options = null);

		/**
		 * filter value for an output (in the given context, could be PHP to "world"; for example json_encode)
		 *
		 * @param mixed         $value
		 * @param null|stdClass $options
		 *
		 * @return mixed
		 */
		public function output($value, ?stdClass $options = null);
	}
