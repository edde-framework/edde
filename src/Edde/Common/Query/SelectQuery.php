<?php
	declare(strict_types=1);
	namespace Edde\Common\Query;

		use Edde\Api\Query\Fragment\ITable;
		use Edde\Api\Query\Fragment\IWhereGroup;
		use Edde\Api\Query\ISelectQuery;
		use Edde\Api\Schema\ISchema;
		use Edde\Common\Query\Fragment\Table;

		class SelectQuery extends AbstractQuery implements ISelectQuery {
			/**
			 * @var ITable
			 */
			protected $table;

			public function __construct(ISchema $schema, string $alias) {
				parent::__construct('SelectQuery');
				$this->table = new Table($schema, $alias);
				$this->select = $alias;
			}

			/**
			 * @inheritdoc
			 */
			public function join(string $schema, string $alias): ISelectQuery {
				$this->table->join($schema, $alias);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function select(string $alias): ISelectQuery {
				$this->table->select($alias);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function where(): IWhereGroup {
				return $this->table->where();
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
