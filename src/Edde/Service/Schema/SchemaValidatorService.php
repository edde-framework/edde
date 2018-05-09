<?php
	declare(strict_types=1);
	namespace Edde\Service\Schema;

	use Edde\Schema\ISchemaValidatorService;

	trait SchemaValidatorService {
		/** @var ISchemaValidatorService */
		protected $schemaValidatorService;

		/**
		 * @param ISchemaValidatorService $schemaValidatorService
		 */
		public function injectSchemaValidatorService(ISchemaValidatorService $schemaValidatorService): void {
			$this->schemaValidatorService = $schemaValidatorService;
		}
	}
