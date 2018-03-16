<?php
	declare(strict_types=1);
	namespace Edde\Inject\Schema;

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
