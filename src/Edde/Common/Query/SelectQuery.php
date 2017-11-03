<?php
	declare(strict_types=1);
	namespace Edde\Common\Query;

		use Edde\Api\Query\Fragment\ITable;
		use Edde\Api\Query\ISelectQuery;
		use Edde\Api\Schema\Inject\SchemaManager;
		use Edde\Api\Schema\ISchema;
		use Edde\Common\Query\Fragment\Table;

		class SelectQuery extends AbstractQuery implements ISelectQuery {
			use SchemaManager;
			/**
			 * @var ITable[]
			 */
			protected $tableList = [];

			public function __construct() {
				parent::__construct('SelectQuery');
			}

			/**
			 * @inheritdoc
			 */
			public function table(ISchema $schema, string $alias): ITable {
				if (isset($this->tableList[$schemaId = ($schema->getName() . $alias)]) === false) {
					$this->tableList[$schemaId] = new Table($schema, $alias);
				}
				return $this->tableList[$schemaId];
			}

			/**
			 * @inheritdoc
			 */
			public function getTableList(): array {
				return $this->tableList;
			}
		}
