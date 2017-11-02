<?php
	declare(strict_types=1);
	namespace Edde\Common\Query;

		use Edde\Api\Query\Fragment\ISchemaFragment;
		use Edde\Api\Query\Fragment\IWhereGroup;
		use Edde\Api\Query\IUpdateQuery;
		use Edde\Api\Schema\ISchema;
		use Edde\Common\Query\Fragment\SchemaFragment;

		class UpdateQuery extends InsertQuery implements IUpdateQuery {
			/**
			 * @var ISchemaFragment
			 */
			protected $schemaFragment;

			public function __construct(ISchema $schema, array $source) {
				parent::__construct($schema, $source);
				$this->type = 'update';
			}

			/**
			 * @inheritdoc
			 */
			public function getSchemaFragment(): ISchemaFragment {
				return $this->schemaFragment ?: $this->schemaFragment = new SchemaFragment($this->schema, 'u');
			}

			/**
			 * @inheritdoc
			 */
			public function hasWhere(): bool {
				return $this->schemaFragment->hasWhere();
			}

			/**
			 * @inheritdoc
			 */
			public function where(): IWhereGroup {
				return $this->getSchemaFragment()->where();
			}
		}
