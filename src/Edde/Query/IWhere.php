<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use stdClass;

	interface IWhere {
		/**
		 * @return IParams
		 */
		public function getParams(): IParams;

		/**
		 * where equal to a value (not to an another property)
		 *
		 * @param string      $alias    schema alias of a property
		 * @param string      $property property of a source alias
		 * @param string|null $param    parameter name reference; if not specified, where name should be used
		 *
		 * @return IWhere
		 */
		public function equalTo(string $alias, string $property, string $param = null): IWhere;

		/**
		 * @param string $alias
		 * @param string $property
		 *
		 * @return IWhere
		 */
		public function isNull(string $alias, string $property): IWhere;

		/**
		 * @param string $alias
		 * @param string $property
		 *
		 * @return IWhere
		 */
		public function isNotNull(string $alias, string $property): IWhere;

		/**
		 * @param string      $alias    schema alias of a property
		 * @param string      $property property of a source alias
		 * @param string|null $param    parameter name reference; if not specified, where name should be used
		 *
		 * @return IWhere
		 */
		public function in(string $alias, string $property, string $param = null): IWhere;

		/**
		 * return internal where object
		 *
		 * @return stdClass
		 */
		public function toObject(): stdClass;
	}
