<?php
	namespace Edde\Api\Schema\Inject;

		use Edde\Api\Schema\ISchemaReflectionService;

		trait SchemaReflectionService {
			/**
			 * @var ISchemaReflectionService
			 */
			protected $schemaReflectionService;

			/**
			 * @param ISchemaReflectionService $schemaReflectionService
			 */
			public function lazySchemaReflectionService(ISchemaReflectionService $schemaReflectionService) {
				$this->schemaReflectionService = $schemaReflectionService;
			}
		}
