<?php
	declare(strict_types=1);
	namespace Edde\Common\Query;

		use Edde\Api\Query\Fragment\ISchemaFragment;
		use Edde\Api\Query\ISelectQuery;
		use Edde\Api\Schema\ISchema;
		use Edde\Common\Query\Fragment\SchemaFragment;

		class SelectQuery extends AbstractQuery implements ISelectQuery {
			/**
			 * @var ISchemaFragment[]
			 */
			protected $schemaFragmentList = [];

			public function __construct() {
				parent::__construct('select');
			}

			/**
			 * @inheritdoc
			 */
			public function schema(ISchema $schema, string $alias): ISchemaFragment {
				if (isset($this->schemaFragmentList[$schemaId = ($schema->getName() . $alias)]) === false) {
					$this->schemaFragmentList[$schemaId] = new SchemaFragment($schema, $alias);
				}
				return $this->schemaFragmentList[$schemaId];
			}

			/**
			 * @inheritdoc
			 */
			public function getSchemaFragmentList(): array {
				return $this->schemaFragmentList;
			}
		}
