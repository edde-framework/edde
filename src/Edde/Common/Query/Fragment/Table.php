<?php
	declare(strict_types=1);
	namespace Edde\Common\Query\Fragment;

		use Edde\Api\Query\Exception\QueryException;
		use Edde\Api\Query\Fragment\ITable;
		use Edde\Api\Query\Fragment\IWhereGroup;
		use Edde\Api\Schema\ISchema;

		class Table extends AbstractFragment implements ITable {
			/**
			 * @var ISchema
			 */
			protected $schema;
			/**
			 * @var string
			 */
			protected $alias;
			/**
			 * @var string
			 */
			protected $select;
			/**
			 * @var IWhereGroup
			 */
			protected $where;
			/**
			 * @var string[]
			 */
			protected $joinList = [];

			public function __construct(ISchema $schema, string $alias) {
				$this->schema = $schema;
				$this->select = $this->alias = $alias;
			}

			/**
			 * @inheritdoc
			 */
			public function select(string $alias): ITable {
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
			public function where(): IWhereGroup {
				if ($this->where === null) {
					$this->where = new WhereGroup();
				}
				return $this->where;
			}

			/**
			 * @inheritdoc
			 */
			public function join(string $schema, string $alias): ITable {
				$this->joinList[$alias] = $schema;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function getJoinList(): array {
				return $this->joinList;
			}
		}
