<?php
	declare(strict_types = 1);

	namespace Edde\Api\Crate;

	use Edde\Api\Schema\ISchemaProperty;

	/**
	 * The physical value of the crate.
	 */
	interface IProperty {
		/**
		 * return value's properties (property definition)
		 *
		 * @return ISchemaProperty
		 */
		public function getSchemaProperty(): ISchemaProperty;

		/**
		 * set value to this property; the original value is preserved
		 *
		 * @param mixed $value
		 *
		 * @return IProperty
		 */
		public function set($value): IProperty;

		/**
		 * push value; the original value is set to this value and current value is nulled; value is NOT dirty after this
		 *
		 * @param mixed $value
		 *
		 * @return IProperty
		 */
		public function push($value): IProperty;

		/**
		 * retrieve current value or default; default value should be updated to the property
		 *
		 * @param mixed|null $default can be callback
		 *
		 * @return mixed
		 */
		public function get($default = null);

		/**
		 * return the original value
		 *
		 * @return mixed
		 */
		public function getValue();

		/**
		 * has been this value changed from the original one?
		 *
		 * @return bool
		 */
		public function isDirty(): bool;

		/**
		 * return true, when property is null (without applying filters)
		 *
		 * @return bool
		 */
		public function isEmpty(): bool;

		/**
		 * forgot current value
		 *
		 * @return IProperty
		 */
		public function reset(): IProperty;
	}
