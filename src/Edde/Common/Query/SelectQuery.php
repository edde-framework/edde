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
				return $this->schemaFragmentList[] = new SchemaFragment($schema, $alias);
			}

			/**
			 * @inheritdoc
			 */
			public function getSchemaFragmentList(): array {
				return $this->schemaFragmentList;
			}

			public function __clone() {
				parent::__clone();
				/**
				 * use the very first schemas as a main schema for
				 * select
				 */
				$this->schemaFragmentList = [reset($this->schemaFragmentList)];
			}
		}
