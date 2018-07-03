<?php
	declare(strict_types=1);

	namespace Edde\Api\Schema\Inject;

	use Edde\Api\Schema\ISchemaManager;

	/**
	 * Defines lazy dependency on a schema manager.
	 */
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
