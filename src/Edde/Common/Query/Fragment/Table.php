<?php
	declare(strict_types=1);
	namespace Edde\Common\Query\Fragment;

		use Edde\Api\Query\Exception\QueryException;
		use Edde\Api\Query\Fragment\ITable;
		use Edde\Api\Query\Fragment\IWhereGroup;
		use Edde\Api\Schema\ISchema;

		class Table extends AbstractFragment implements ITable {
			/**
			 * schema used for this table
			 *
			 * @var ISchema
			 */
			protected $schema;
			/**
			 * alias of this table
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
			 * which alias is selected for the query (select $alis.*)
			 *
			 * @var string
			 */
			protected $select;
			/**
			 * where clause
			 *
			 * @var IWhereGroup
			 */
			protected $where;
			/**
			 * @var array
			 */
			protected $link;
			/**
			 * list of joins for this table
			 *
			 * @var string[]
			 */
			protected $joins = [];
			/**
			 * @var string[]
			 */
			protected $orders = [];

			public function __construct(ISchema $schema, string $alias) {
				$this->schema = $schema;
				$this->current = $this->select = $this->alias = $alias;
			}

			/**
			 * @inheritdoc
			 */
			public function select(string $alias = null): ITable {
				$alias = $alias ?: $this->current;
				if (isset($this->joins[$alias]) === false && isset($this->link[$alias]) === false && $this->alias !== $alias) {
					throw new QueryException(sprintf('Cannot select unknown alias [%s]; choose select alias [%s] or one of joined aliases [%s].', $alias, $this->getAlias(), implode(', ', array_keys($this->joins))));
				}
				$this->select = $alias;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function getSelect(): string {
				return $this->select;
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
			public function hasWhere(): bool {
				return $this->where !== null;
			}

			/**
			 * @inheritdoc
			 */
			public function link(string $schema, string $alias, array $source): ITable {
				$this->link[$this->current = $alias] = [$schema, $alias, $source];
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function hasLink(): bool {
				return $this->link !== null;
			}

			/**
			 * @inheritdoc
			 */
			public function getLink(): array {
				return $this->link;
			}

			/**
			 * @inheritdoc
			 */
			public function join(string $schema, string $alias): ITable {
				$this->joins[$this->current = $alias] = $schema;
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
			public function where(): IWhereGroup {
				if ($this->where === null) {
					$this->where = new WhereGroup();
				}
				return $this->where;
			}

			/**
			 * @inheritdoc
			 */
			public function order(string $name, bool $asc = true): ITable {
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
		}
