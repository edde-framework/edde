<?php
	declare(strict_types=1);
	namespace Edde\Hydrator;

	use Edde\Schema\ISchema;

	class SchemaHydrator extends AbstractHydrator {
		/** @var ISchema */
		protected $schema;

		/**
		 * @param ISchema $schema
		 */
		public function __construct(ISchema $schema) {
			$this->schema = $schema;
		}

		/** @inheritdoc */
		public function hydrate(array $source) {
		}
	}
