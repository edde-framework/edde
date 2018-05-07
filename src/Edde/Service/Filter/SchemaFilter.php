<?php
	declare(strict_types=1);
	namespace Edde\Service\Filter;

	use Edde\Filter\ISchemaFilter;

	trait SchemaFilter {
		/** @var ISchemaFilter */
		protected $schemaFilter;

		/**
		 * @param ISchemaFilter $schemaFilter
		 */
		public function injectSchemaFilter(ISchemaFilter $schemaFilter): void {
			$this->schemaFilter = $schemaFilter;
		}
	}
