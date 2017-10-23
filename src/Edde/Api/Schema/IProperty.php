<?php
	namespace Edde\Api\Schema;

		use Edde\Api\Node\INode;

		interface IProperty {
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
			 * property type
			 *
			 * @param string $type
			 *
			 * @return IProperty
			 */
			public function type(string $type): IProperty;
		}
