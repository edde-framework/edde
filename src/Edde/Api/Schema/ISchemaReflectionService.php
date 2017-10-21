<?php
	namespace Edde\Api\Schema;

		use Edde\Api\Config\IConfigurable;
		use Edde\Api\Schema\Exception\SchemaReflectionException;

		/**
		 * Magical service with ability to extract a schema from a given class name (or general
		 * identifier).
		 */
		interface ISchemaReflectionService extends IConfigurable {
			/**
			 * try to extract a schema from the given name (class reflection should be used)
			 *
			 * @param string $name
			 *
			 * @return ISchema
			 *
			 * @throws SchemaReflectionException
			 */
			public function getSchema(string $name): ISchema;
		}
