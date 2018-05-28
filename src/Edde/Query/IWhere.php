<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use stdClass;

	interface IWhere {
		/**
		 * where equal to a value (not to an another property)
		 *
		 * @param string $alias    schema alias of a property
		 * @param string $property property of a source alias
		 * @param mixed  $value    simple scalar value
		 *
		 * @return IWhere
		 */
		public function equalTo(string $alias, string $property, $value): IWhere;

		/**
		 * return internal where object
		 *
		 * @return stdClass
		 */
		public function toObject(): stdClass;
	}
