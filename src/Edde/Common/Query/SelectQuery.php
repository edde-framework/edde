<?php
	declare(strict_types=1);
	namespace Edde\Common\Query;

		use Edde\Api\Query\Fragment\ITable;
		use Edde\Api\Query\ISelectQuery;
		use Edde\Api\Schema\ISchema;

		class SelectQuery extends AbstractQuery implements ISelectQuery {
			/**
			 * @var ISchema
			 */
			protected $schema;
			/**
			 * @var string
			 */
			protected $alias;
			/**
			 * @var ITable[]
			 */
			protected $joinList = [];

			public function __construct(ISchema $schema, string $alias) {
				parent::__construct('SelectQuery');
				$this->schema = $schema;
				$this->alias = $alias;
			}

			/**
			 * @inheritdoc
			 */
			public function join(): ITable {

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
			public function getAlias(): string {
				return $this->alias;
			}
		}
