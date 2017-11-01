<?php
	namespace Edde\Common\Query;

		use Edde\Api\Query\ICrateSchemaQuery;
		use Edde\Api\Schema\ISchema;

		class CreateSchemaQuery extends AbstractQuery implements ICrateSchemaQuery {
			/**
			 * @var ISchema
			 */
			protected $schema;

			public function __construct(ISchema $schema) {
				parent::__construct('create-schema');
				$this->schema = $schema;
			}

			/**
			 * @inheritdoc
			 */
			public function getSchema(): ISchema {
				return $this->schema;
			}
		}
