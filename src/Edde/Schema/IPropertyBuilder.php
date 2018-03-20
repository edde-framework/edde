<?php
	declare(strict_types=1);
	namespace Edde\Schema;

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
		 * @param string $generator
		 *
		 * @return IPropertyBuilder
		 */
		public function generator(string $generator): IPropertyBuilder;

		/**
		 * set the name of filter responsible for value filtering of this property
		 *
		 * @param string $filter
		 *
		 * @return IPropertyBuilder
		 */
		public function filter(string $filter): IPropertyBuilder;

		/**
		 * set a sanitizer for this property
		 *
		 * @param string $sanitizer
		 *
		 * @return IPropertyBuilder
		 */
		public function sanitizer(string $sanitizer): IPropertyBuilder;

		/**
		 * set a validator for this property
		 *
		 * @param string $validator
		 *
		 * @return IPropertyBuilder
		 */
		public function validator(string $validator): IPropertyBuilder;

		/**
		 * mark property as a link (just a flag)
		 *
		 * @return IPropertyBuilder
		 */
		public function link(): IPropertyBuilder;

		/**
		 * set a default value for this property
		 *
		 * @param mixed $default
		 *
		 * @return IPropertyBuilder
		 */
		public function default($default): IPropertyBuilder;

		/**
		 * creates and return a property
		 *
		 * @return IProperty
		 */
		public function getProperty(): IProperty;
	}
