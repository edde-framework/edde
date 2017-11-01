<?php
	declare(strict_types=1);
	namespace Edde\Api\Crate;

	/**
	 * Crate property.
	 */
		interface IProperty {
			/**
			 * get the name of a property
			 *
			 * @return string
			 */
			public function getName(): string;

			/**
			 * set this value as default
			 *
			 * @param mixed $value
			 *
			 * @return IProperty
			 */
			public function setDefault($value): IProperty;

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
			public function setValue($value): IProperty;

			/**
			 * get a (current) value from this property
			 *
			 * @param mixed $default
			 *
			 * @return mixed
			 */
			public function getValue($default = null);

			/**
			 * has property some value? Only NULL should be considered as empty
			 *
			 * @return bool
			 */
			public function isEmpty(): bool;

			/**
			 * resolve value; if current value is empty/null, default is checked; if same, default parameter is used
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

			/**
			 * set current value as default, thus property no longer will be dirty
			 *
			 * @return IProperty
			 */
			public function commit(): IProperty;
		}
