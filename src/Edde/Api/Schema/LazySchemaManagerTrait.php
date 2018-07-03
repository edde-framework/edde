<?php
	declare(strict_types = 1);

	namespace Edde\Api\Schema;

	/**
	 * Defines lazy dependency on a schema manager.
	 */
	trait LazySchemaManagerTrait {
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
