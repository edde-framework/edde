<?php
	declare(strict_types=1);
	namespace Edde\Service\Schema;

	use Edde\Schema\ISchemaFilterService;

	trait SchemaFilterService {
		/** @var ISchemaFilterService */
		protected $schemaFilterService;

		/**
		 * @param ISchemaFilterService $schemaFilterService
		 */
		public function injectSchemaFilterService(ISchemaFilterService $schemaFilterService): void {
			$this->schemaFilterService = $schemaFilterService;
		}
	}
