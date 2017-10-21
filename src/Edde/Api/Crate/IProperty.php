<?php
	namespace Edde\Api\Crate;

	/**
	 * Crate property.
	 */
		interface IProperty {
			/**
			 * set this value as default
			 *
			 * @param mixed $value
			 *
			 * @return IProperty
			 */
			public function default($value): IProperty;

			/**
			 * return default value of this property
			 *
			 * @return mixed
			 */
			public function getDefault();

			/**
			 * set value of this property; if it's not reset to the original value, it's marked as dirty
			 *
			 * @param mixed $value
			 *
			 * @return IProperty
			 */
			public function set($value): IProperty;

			/**
			 * get a (current) value from this property
			 *
			 * @param mixed $default
			 *
			 * @return mixed
			 */
			public function get($default = null);

			/**
			 * has property been changed?
			 *
			 * @return bool
			 */
			public function isDirty(): bool;
		}
