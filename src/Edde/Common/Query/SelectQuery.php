<?php
	declare(strict_types=1);
	namespace Edde\Common\Query;

		use Edde\Api\Query\Exception\QueryException;
		use Edde\Api\Query\Fragment\IJoin;
		use Edde\Api\Query\Fragment\IWhereGroup;
		use Edde\Api\Query\ISelectQuery;
		use Edde\Api\Schema\ISchema;
		use Edde\Common\Query\Fragment\Join;
		use Edde\Common\Query\Fragment\WhereGroup;

		class SelectQuery extends AbstractQuery implements ISelectQuery {
			/**
			 * @var ISchema
			 */
			protected $schema;
			/**
			 * alias assigned to source schema
			 *
			 * @var string
			 */
			protected $alias;
			/**
			 * current alias
			 *
			 * @var string
			 */
			protected $current;
			/**
			 * @var IJoin[]
			 */
			protected $joins = [];
			/**
			 * @var IWhereGroup
			 */
			protected $where;
			/**
			 * @var string[]
			 */
			protected $orders = [];
			/**
			 * which alias will be returned as a query result
			 *
			 * @var string
			 */
			protected $return;

			public function __construct(ISchema $schema, string $alias) {
				$this->schema = $schema;
				$this->alias = $alias;
			}

			/**
			 * @inheritdoc
			 */
			public function getAlias(): string {
				return $this->alias;
			}

			/**
			 * @inheritdoc
			 */
			public function link(string $schema, string $alias): ISelectQuery {
				$this->joins[$this->current = $alias] = new Join($schema, $alias, true);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function join(string $schema, string $alias): ISelectQuery {
				$this->joins[$this->current = $alias] = new Join($schema, $alias);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function return(string $alias = null): ISelectQuery {
				$alias = $this->alias ?: $this->current;
				if (isset($this->joins[$alias]) === false && $this->alias !== $alias) {
					throw new QueryException(sprintf('Cannot select unknown alias [%s]; choose select alias [%s] or one of joined aliases [%s].', $alias, $this->getAlias(), implode(', ', array_keys($this->joins))));
				}
				$this->return = $alias;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function where(string $name, string $relation, $value): ISelectQuery {
				if (($dot = strpos($name, '.')) === false) {
					$name = $this->alias . '.' . $name;
				}
				($this->where ?: $this->where = new WhereGroup())->and()->value($name, $relation, $value);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function order(string $name, bool $asc = true): ISelectQuery {
				if (($dot = strpos($name, '.')) === false) {
					$name = $this->alias . '.' . $name;
				}
				$this->orders[$name] = $asc;
				return $this;
			}
		}
