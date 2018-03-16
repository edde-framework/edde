<?php
	declare(strict_types=1);
	namespace Edde\Common\Storage\Query;

	use Edde\Schema\ISchema;
	use Edde\Storage\Query\ICrateSchemaQuery;

	class CreateSchemaQuery extends AbstractQuery implements ICrateSchemaQuery {
		/**
		 * @var \Edde\Schema\ISchema
		 */
		protected $schema;

		public function __construct(ISchema $schema) {
			$this->schema = $schema;
		}

		/**
		 * @inheritdoc
		 */
		public function getSchema(): ISchema {
			return $this->schema;
		}
	}
