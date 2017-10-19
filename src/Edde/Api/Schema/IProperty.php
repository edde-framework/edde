<?php
	namespace Edde\Api\Schema;

		interface IProperty {
			/**
			 * shortcut for required
			 *
			 * @return IProperty
			 */
			public function primary(): IProperty;

			/**
			 * set property value generator; if a value is not set, this generator should be used
			 * to get a property value
			 *
			 * @param string $string
			 *
			 * @return IProperty
			 */
			public function generator(string $string): IProperty;

			/**
			 * set property as it's required
			 *
			 * @return IProperty
			 */
			public function required(): IProperty;

			/**
			 * property type
			 *
			 * @param string $type
			 *
			 * @return IProperty
			 */
			public function type(string $type): IProperty;
		}
