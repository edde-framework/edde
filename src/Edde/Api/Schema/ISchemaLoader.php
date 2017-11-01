<?php
	declare(strict_types=1);
	namespace Edde\Api\Schema;

		interface ISchemaLoader {
			/**
			 * try to load schema with the given name; return null of not available
			 *
			 * @param string $schema
			 *
			 * @return ISchema|null
			 */
			public function getSchema(string $schema): ?ISchema;
		}
