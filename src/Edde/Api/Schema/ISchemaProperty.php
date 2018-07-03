<?php
	declare(strict_types = 1);

	namespace Edde\Api\Schema;

	/**
	 * Definition of a schema property.
	 */
	interface ISchemaProperty {
		/**
		 * return schema to which this property belongs
		 *
		 * @return ISchema
		 */
		public function getSchema(): ISchema;

		/**
		 * return name of this property
		 *
		 * @return string
		 */
		public function getName(): string;

		/**
		 * return full name of property, including schema and namespace
		 *
		 * @return string
		 */
		public function getPropertyName(): string;

		/**
		 * is this property part of schema's identity?
		 *
		 * @return bool
		 */
		public function isIdentifier(): bool;

		/**
		 * @return string
		 */
		public function getType(): string;

		/**
		 * is value of this property required?
		 *
		 * @return bool
		 */
		public function isRequired(): bool;

		/**
		 * has to be value of this property in it's schema unique?
		 *
		 * @return bool
		 */
		public function isUnique(): bool;

		/**
		 * array of scalar types
		 *
		 * @return bool
		 */
		public function isArray(): bool;

		/**
		 * has this property generator?
		 *
		 * @return bool
		 */
		public function hasGenerator(): bool;

		/**
		 * generate value; if property has no generator, exception should be thrown
		 */
		public function generator();

		/**
		 * if the value has some filters, use them
		 *
		 * @param mixed $value
		 *
		 * @return mixed
		 */
		public function filter($value);

		/**
		 * execute filters for a setter value
		 *
		 * @param mixed $value
		 *
		 * @return mixed
		 */
		public function setterFilter($value);

		/**
		 * execute filters for a getter value
		 *
		 * @param mixed $value
		 *
		 * @return mixed
		 */
		public function getterFilter($value);

		/**
		 * tells if the values are different but respects property type (e.g. "100" === 100 when property is int/float)
		 *
		 * @param mixed $current
		 * @param mixed $value
		 *
		 * @return bool
		 */
		public function isDirty($current, $value): bool;
	}
