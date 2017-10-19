<?php
	namespace Edde\Api\Schema;

		use Edde\Api\Config\IConfigurable;

		interface ISchema extends IConfigurable {
			/**
			 * return name of a schema (it could have even "namespace" like name)
			 *
			 * @return string
			 */
			public function getName(): string;

			/**
			 * create a primary propery on the schema
			 *
			 * @param string $name
			 *
			 * @return IProperty
			 */
			public function primary(string $name): IProperty;

			/**
			 * create a string type property with the given name
			 *
			 * @param string $name
			 *
			 * @return IProperty
			 */
			public function string(string $name): IProperty;
		}
