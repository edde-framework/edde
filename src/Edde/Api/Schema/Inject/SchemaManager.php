<?php
	namespace Edde\Api\Schema\Inject;

		use Edde\Api\Schema\ISchemaManager;

		trait SchemaManager {
			/**
			 * @var ISchemaManager
			 */
			protected $schemaManager;

			/**
			 * @param ISchemaManager $schemaManager
			 */
			public function lazySchemaManager(ISchemaManager $schemaManager) {
				$this->schemaManager = $schemaManager;
			}
		}
