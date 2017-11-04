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
			protected $alias;

			public function __construct(ISchema $schema, string $alias) {
				parent::__construct('SelectQuery');
				$this->table = new Table($schema, $alias);
				$this->alias = $alias;
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
			public function select(string $alias): ISelectQuery {
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
//				if (($dot = strpos($name, '.')) !== false) {
//					$alias = substr($name, 0, $dot);
//					$name = substr($name, $dot + 1);
//				}
				$this->table->where()->and()->value($name, '=', $value);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function getTable(): ITable {
				return $this->table;
			}
		}
