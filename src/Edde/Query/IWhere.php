<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use stdClass;

	interface IWhere {
		/**
		 * where name
		 *
		 * @return string
		 */
		public function getName(): string;

		/**
		 * where equal to a value (not to an another property)
		 *
		 * @param string $alias    schema alias of a property
		 * @param string $property property of a source alias
		 * @param string $param    parameter name reference; if not specified, where name should be used
		 *
		 * @return IWhere
		 */
		public function equalTo(string $alias, string $property, string $param = null): IWhere;

		/**
		 * return internal where object
		 *
		 * @return stdClass
		 */
		public function toObject(): stdClass;
	}
