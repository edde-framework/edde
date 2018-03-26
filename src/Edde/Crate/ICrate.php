<?php
	declare(strict_types=1);
	namespace Edde\Crate;

	use Edde\Schema\ISchema;
	use Edde\Schema\SchemaException;
	use stdClass;

	/**
	 * Crate is very simple implementation of object able to track it's changes and do some common stuff
	 * usually ORM objects do, but in abstract way. Crate with a Schema is an Entity.
	 */
	interface ICrate {
		/**
		 * @return ISchema
		 */
		public function getSchema(): ISchema;

		/**
		 * when setting a property to crate, value should be compatible type to keep
		 * everything working properly; that's scalar types, DateTime and few others
		 *
		 * @param string $property
		 * @param mixed  $value
		 *
		 * @return ICrate
		 */
		public function set(string $property, $value): ICrate;

		/**
		 * get an (effective) value
		 *
		 * @param string $property
		 * @param mixed  $default
		 *
		 * @return mixed
		 */
		public function get(string $property, $default = null);

		/**
		 * does property with the given name exists in this crate?
		 *
		 * @param string $name
		 *
		 * @return bool
		 */
		public function hasProperty(string $name): bool;

		/**
		 * return primary property of this entity
		 *
		 * @return IProperty
		 *
		 * @throws SchemaException
		 */
		public function getPrimary(): IProperty;

		/**
		 * return or create property with the given name
		 *
		 * @param string $name
		 *
		 * @return IProperty
		 *
		 * @throws SchemaException
		 */
		public function getProperty(string $name): IProperty;

		/**
		 * update the crate with the given data; the crate could be dirty after; put should
		 * drop current properties and create new ones
		 *
		 * @param stdClass $source
		 *
		 * @return $this
		 *
		 * @throws SchemaException
		 */
		public function put(stdClass $source): ICrate;

		/**
		 * push given data to the crate without making it dirty (if it was already dirty,
		 * it could be "cleaned")
		 *
		 * @param stdClass $source
		 *
		 * @return $this
		 *
		 * @throws SchemaException
		 */
		public function push(stdClass $source): ICrate;

		/**
		 * set all values for properties as "default", thus making crate not dirty
		 *
		 * @return ICrate
		 */
		public function commit(): ICrate;

		/**
		 * is state of a crate changed (any property is changed?)?
		 *
		 * @return bool
		 */
		public function isDirty(): bool;

		/**
		 * return an array with dirty properties
		 *
		 * @return IProperty[]
		 */
		public function getDirtyProperties(): array;

		/**
		 * check if all properties of this crate are empty
		 *
		 * @return bool
		 */
		public function isEmpty(): bool;

		/**
		 * return crate as an array
		 *
		 * @return stdClass
		 *
		 * @throws SchemaException
		 */
		public function toObject(): stdClass;
	}
