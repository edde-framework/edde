<?php
	declare(strict_types=1);
	namespace Edde\Common\Query\Fragment;

		use Edde\Api\Entity\IEntity;
		use Edde\Api\Query\Exception\QueryException;
		use Edde\Api\Query\Fragment\ITable;
		use Edde\Api\Query\Fragment\IWhereGroup;
		use Edde\Api\Schema\ILink;
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
			 * list of joins for this table
			 *
			 * @var string[]
			 */
			protected $joinList = [];
			/**
			 * @var string[]
			 */
			protected $orderList = [];

			public function __construct(ISchema $schema, string $alias) {
				$this->schema = $schema;
				$this->current = $this->select = $this->alias = $alias;
			}

			/**
			 * @inheritdoc
			 */
			public function select(string $alias = null): ITable {
				$alias = $alias ?: $this->current;
				if (isset($this->joinList[$alias]) === false && $this->alias !== $alias) {
					throw new QueryException(sprintf('Cannot select unknown alias [%s]; choose select alias [%s] or one of joined aliases [%s].', $alias, $this->table->getAlias(), implode(', ', array_keys($this->joinList))));
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
			public function link(IEntity $entity, ILink $link): ITable {
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function join(string $schema, string $alias): ITable {
				$this->joinList[$this->current = $alias] = $schema;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function getJoinList(): array {
				return $this->joinList;
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
				$this->orderList[$name] = $asc;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function hasOrder(): bool {
				return empty($this->orderList) === false;
			}

			/**
			 * @inheritdoc
			 */
			public function getOrderList(): array {
				return $this->orderList;
			}
		}
