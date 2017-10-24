<?php
	namespace Edde\Api\Crate;

	/**
	 * Crate is very simple implementation of object able to track it's changes and do some common stuff
	 * usually ORM objects do, but in abstract way. Crate with a Schema is an Entity.
	 */
		interface ICrate {
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
			 * return or create property with the given name
			 *
			 * @param string $name
			 *
			 * @return IProperty
			 */
			public function getProperty(string $name): IProperty;

			/**
			 * update the crate with the given data; the crate will be dirty after
			 *
			 * @param array $source
			 *
			 * @return $this
			 */
			public function update(array $source): ICrate;

			/**
			 * push given data to the crate without making it dirty (if it was already dirty,
			 * it could be "cleaned")
			 *
			 * @param array $source
			 *
			 * @return $this
			 */
			public function push(array $source): ICrate;

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
		}
