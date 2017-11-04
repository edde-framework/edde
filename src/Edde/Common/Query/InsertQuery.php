<?php
	declare(strict_types=1);
	namespace Edde\Common\Query;

		use Edde\Api\Query\IInsertQuery;
		use Edde\Api\Schema\ISchema;

		class InsertQuery extends AbstractQuery implements IInsertQuery {
			/**
			 * @var ISchema
			 */
			protected $schema;
			/**
			 * @var array
			 */
			protected $source;

			public function __construct(ISchema $schema, array $source) {
				$this->schema = $schema;
				$this->source = $source;
			}

			/**
			 * @inheritdoc
			 */
			public function getSchema(): ISchema {
				return $this->schema;
			}

			/**
			 * @inheritdoc
			 */
			public function getSource(): array {
				return $this->source;
			}
		}
