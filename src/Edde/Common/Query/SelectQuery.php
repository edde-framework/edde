<?php
	declare(strict_types=1);
	namespace Edde\Common\Query;

		use Edde\Api\Query\Fragment\ITable;
		use Edde\Api\Query\ISelectQuery;
		use Edde\Api\Schema\ISchema;
		use Edde\Common\Query\Fragment\Table;

		class SelectQuery extends AbstractQuery implements ISelectQuery {
			/**
			 * @var ITable
			 */
			protected $table;
			/**
			 * @var string
			 */
			protected $alias;

			public function __construct(ISchema $schema, string $alias) {
				$this->table = new Table($schema, $alias);
				$this->alias = $alias;
			}

			/**
			 * @inheritdoc
			 */
			public function link(string $schema, string $alias, array $source): ISelectQuery {
				$this->table->link($schema, $alias, $source);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function join(string $schema, string $alias): ISelectQuery {
				$this->table->join($schema, $this->alias = $alias);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function select(string $alias = null): ISelectQuery {
				$this->table->select($alias);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function where(string $name, string $relation, $value): ISelectQuery {
				if (($dot = strpos($name, '.')) === false) {
					$name = $this->alias . '.' . $name;
				}
				$this->table->where()->and()->value($name, $relation, $value);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function order(string $name, bool $asc = true): ISelectQuery {
				if (($dot = strpos($name, '.')) === false) {
					$name = $this->alias . '.' . $name;
				}
				$this->table->order($name, $asc);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function getTable(): ITable {
				return $this->table;
			}
		}
