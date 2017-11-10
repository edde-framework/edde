<?php
	declare(strict_types=1);
	namespace Edde\Common\Entity\Query;

		use Edde\Api\Entity\Query\ICrateSchemaQuery;
		use Edde\Api\Schema\ISchema;

		class CreateSchemaQuery extends AbstractQuery implements ICrateSchemaQuery {
			/**
			 * @var ISchema
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
