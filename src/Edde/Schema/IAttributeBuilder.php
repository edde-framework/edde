<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	interface IAttributeBuilder {
		/**
		 * set an attribute type
		 *
		 * @param string $type
		 *
		 * @return IAttributeBuilder
		 */
		public function type(string $type): IAttributeBuilder;

		/**
		 * set a property as it's value is unique in it's schema
		 *
		 * @param bool $unique
		 *
		 * @return IAttributeBuilder
		 */
		public function unique(bool $unique = true): IAttributeBuilder;

		/**
		 * shortcut for required and unique
		 *
		 * @param bool $primary
		 *
		 * @return IAttributeBuilder
		 */
		public function primary(bool $primary = true): IAttributeBuilder;

		/**
		 * set property as it's required
		 *
		 * @param bool $required
		 *
		 * @return IAttributeBuilder
		 */
		public function required(bool $required = true): IAttributeBuilder;

		/**
		 * name of value generator for this property (if value is null)
		 *
		 * @param string $generator
		 *
		 * @return IAttributeBuilder
		 */
		public function generator(string $generator): IAttributeBuilder;

		/**
		 * set the name of filter responsible for value filtering of this property
		 *
		 * @param string $filter
		 *
		 * @return IAttributeBuilder
		 */
		public function filter(string $filter): IAttributeBuilder;

		/**
		 * set a sanitizer for this property
		 *
		 * @param string $sanitizer
		 *
		 * @return IAttributeBuilder
		 */
		public function sanitizer(string $sanitizer): IAttributeBuilder;

		/**
		 * set a validator for this property
		 *
		 * @param string $validator
		 *
		 * @return IAttributeBuilder
		 */
		public function validator(string $validator): IAttributeBuilder;

		/**
		 * mark property as a link (just a flag)
		 *
		 * @return IAttributeBuilder
		 */
		public function link(): IAttributeBuilder;

		/**
		 * set a default value for this property
		 *
		 * @param mixed $default
		 *
		 * @return IAttributeBuilder
		 */
		public function default($default): IAttributeBuilder;

		/**
		 * creates and return a property
		 *
		 * @return IAttribute
		 */
		public function getAttribute(): IAttribute;
	}
