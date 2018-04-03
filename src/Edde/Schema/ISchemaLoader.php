<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	interface ISchemaLoader {
		/**
		 * try to load schema with the given name
		 *
		 * @param string $schema
		 *
		 * @return ISchema
		 *
		 * @throws SchemaException
		 */
		public function load(string $schema): ISchema;
	}
