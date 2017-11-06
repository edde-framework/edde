<?php
	declare(strict_types=1);
	namespace Edde\Api\Schema;

		interface IPropertyBuilder {
			/**
			 * set property type
			 *
			 * @param string $type
			 *
			 * @return IPropertyBuilder
			 */
			public function type(string $type): IPropertyBuilder;

			/**
			 * set a property as it's value is unique in it's schema
			 *
			 * @param bool $unique
			 *
			 * @return IPropertyBuilder
			 */
			public function unique(bool $unique = true): IPropertyBuilder;

			/**
			 * shortcut for required and unique
			 *
			 * @param bool $primary
			 *
			 * @return IPropertyBuilder
			 */
			public function primary(bool $primary = true): IPropertyBuilder;

			/**
			 * set property as it's required
			 *
			 * @param bool $required
			 *
			 * @return IPropertyBuilder
			 */
			public function required(bool $required = true): IPropertyBuilder;

			/**
			 * name of value generator for this property (if value is null)
			 *
			 * @param string $name
			 *
			 * @return IPropertyBuilder
			 */
			public function generator(string $name): IPropertyBuilder;

			/**
			 * set the name of filter responsible for value filtering of this property
			 *
			 * @param string $name
			 *
			 * @return IPropertyBuilder
			 */
			public function filter(string $name): IPropertyBuilder;

			/**
			 * set a sanitizer for this property
			 *
			 * @param string $name
			 *
			 * @return IPropertyBuilder
			 */
			public function sanitizer(string $name): IPropertyBuilder;

			/**
			 * creates and return a property
			 *
			 * @return IProperty
			 */
			public function getProperty(): IProperty;
		}
