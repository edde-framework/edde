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
			 * @return IProperty
			 */
			public function primary(): IProperty;

			/**
			 * set a property as it's value is unique in it's schema
			 *
			 * @return IProperty
			 */
			public function unique(): IProperty;

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
