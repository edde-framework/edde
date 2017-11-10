<?php
	declare(strict_types=1);
	namespace Edde\Common\Entity\Query;

		use Edde\Api\Entity\Exception\QueryException;
		use Edde\Api\Entity\Query\Fragment\IJoin;
		use Edde\Api\Entity\Query\Fragment\IWhereGroup;
		use Edde\Api\Entity\Query\ISelectQuery;
		use Edde\Api\Schema\ISchema;
		use Edde\Common\Entity\Query\Fragment\Join;
		use Edde\Common\Entity\Query\Fragment\WhereGroup;

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
				$this->return = $this->alias = $alias;
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
			public function getJoins(): array {
				return $this->joins;
			}

			/**
			 * @inheritdoc
			 */
			public function getReturn(): string {
				return $this->return;
			}

			/**
			 * @inheritdoc
			 */
			public function where(string $name, string $relation, $value): ISelectQuery {
				if (($dot = strpos($name, '.')) === false) {
					$name = $this->alias . '.' . $name;
				}
				$this->getWhere()->and()->value($name, $relation, $value);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function hasWhere(): bool {
				return $this->where !== null;
			}

			/**
			 * @inheritdoc
			 */
			public function getWhere(): IWhereGroup {
				return $this->where ?: $this->where = new WhereGroup();
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

			/**
			 * @inheritdoc
			 */
			public function hasOrder(): bool {
				return empty($this->orders) === false;
			}

			/**
			 * @inheritdoc
			 */
			public function getOrders(): array {
				return $this->orders;
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
		}
