<?php
	declare(strict_types = 1);

	namespace Edde\Api\Schema;

	/**
	 * LAzy dependency on a schema cache.
	 */
	trait LazySchemaFactoryTrait {
		/**
		 * @var ISchemaFactory
		 */
		protected $schemaFactory;

		/**
		 * @param ISchemaFactory $schemaFactory
		 */
		public function lazySchemaFactory(ISchemaFactory $schemaFactory) {
			$this->schemaFactory = $schemaFactory;
		}
	}
