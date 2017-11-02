<?php
	declare(strict_types=1);
	namespace Edde\Common\Query;

		use Edde\Api\Query\Fragment\ISchemaFragment;
		use Edde\Api\Query\ISelectQuery;
		use Edde\Api\Schema\Inject\SchemaManager;
		use Edde\Common\Query\Fragment\SchemaFragment;

		class SelectQuery extends AbstractQuery implements ISelectQuery {
			use SchemaManager;
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
			public function schema(string $schema, string $alias): ISchemaFragment {
				if (isset($this->schemaFragmentList[$schemaId = ($schema . $alias)]) === false) {
					$this->schemaFragmentList[$schemaId] = new SchemaFragment($this->schemaManager->load($schema), $alias);
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
