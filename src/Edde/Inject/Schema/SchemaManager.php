<?php
	declare(strict_types=1);
	namespace Edde\Inject\Schema;

	use Edde\Schema\ISchemaManager;

	trait SchemaManager {
		/**
		 * @var \Edde\Schema\ISchemaManager
		 */
		protected $schemaManager;

		/**
		 * @param ISchemaManager $schemaManager
		 */
		public function lazySchemaManager(ISchemaManager $schemaManager) {
			$this->schemaManager = $schemaManager;
		}
	}
