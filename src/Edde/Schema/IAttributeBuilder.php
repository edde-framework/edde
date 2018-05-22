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
		 * @param string $type
		 * @param string $filter
		 *
		 * @return IAttributeBuilder
		 */
		public function filter(string $type, string $filter): IAttributeBuilder;

		/**
		 * set a validator for this property
		 *
		 * @param string $validator
		 *
		 * @return IAttributeBuilder
		 */
		public function validator(string $validator): IAttributeBuilder;

		/**
		 * set a default value for this property
		 *
		 * @param mixed $default
		 *
		 * @return IAttributeBuilder
		 */
		public function default($default): IAttributeBuilder;

		/**
		 * mark this attribute as a source of relation; (source)->(target)
		 *
		 * @param string $source
		 *
		 * @return IAttributeBuilder
		 */
		public function source(string $source): IAttributeBuilder;

		/**
		 * mark this attribute as a target of relation; (source)->(target)
		 *
		 * @param string $target
		 *
		 * @return IAttributeBuilder
		 */
		public function target(string $target): IAttributeBuilder;

		/**
		 * creates and return a property
		 *
		 * @return IAttribute
		 */
		public function getAttribute(): IAttribute;
	}
