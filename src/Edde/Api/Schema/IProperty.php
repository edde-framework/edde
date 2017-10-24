<?php
	namespace Edde\Api\Schema;

		use Edde\Api\Node\INode;

		interface IProperty {
			/**
			 * get name of this property
			 *
			 * @return string
			 */
			public function getName(): string;

			/**
			 * return data node of this property
			 *
			 * @return INode
			 */
			public function getNode(): INode;

			/**
			 * shortcut for required and unique
			 *
			 * @param bool $primary
			 *
			 * @return IProperty
			 */
			public function primary(bool $primary = true): IProperty;

			/**
			 * is this property marked as primary?
			 *
			 * @return bool
			 */
			public function isPrimary(): bool;

			/**
			 * name of value generator for this property (if value is null)
			 *
			 * @param string $name
			 *
			 * @return IProperty
			 */
			public function generator(string $name): IProperty;

			/**
			 * @return string
			 */
			public function getGenerator(): ?string;

			/**
			 * set the name of filter responsible for value filtering of this property
			 *
			 * @param string $name
			 *
			 * @return IProperty
			 */
			public function filter(string $name): IProperty;

			/**
			 * get property filter
			 *
			 * @return null|string
			 */
			public function getFilter(): ?string;

			/**
			 * set a sanitizer for this property
			 *
			 * @param string $name
			 *
			 * @return IProperty
			 */
			public function sanitizer(string $name): IProperty;

			/**
			 * return name of the sanitizer for this property
			 *
			 * @return null|string
			 */
			public function getSanitizer(): ?string;

			/**
			 * set a property as it's value is unique in it's schema
			 *
			 * @param bool $unique
			 *
			 * @return IProperty
			 */
			public function unique(bool $unique = true): IProperty;

			/**
			 * is this property marked as unique?
			 *
			 * @return bool
			 */
			public function isUnique(): bool;

			/**
			 * set property as it's required
			 *
			 * @param bool $required
			 *
			 * @return IProperty
			 */
			public function required(bool $required = true): IProperty;

			/**
			 * set property type
			 *
			 * @param string $type
			 *
			 * @return IProperty
			 */
			public function type(string $type): IProperty;

			/**
			 * get type of property
			 *
			 * @return string
			 */
			public function getType(): string;
		}
