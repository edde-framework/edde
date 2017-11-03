<?php
	declare(strict_types=1);
	namespace Edde\Common\Query\Fragment;

		use Edde\Api\Query\Fragment\ITable;
		use Edde\Api\Query\Fragment\IWhereGroup;
		use Edde\Api\Schema\IRelation;
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
			 * @var bool
			 */
			protected $selected = false;
			/**
			 * @var IWhereGroup
			 */
			protected $where;
			/**
			 * @var IRelation[]
			 */
			protected $joinList = [];

			public function __construct(ISchema $schema, string $alias) {
				parent::__construct('Table');
				$this->schema = $schema;
				$this->alias = $alias;
			}

			/**
			 * @inheritdoc
			 */
			public function select(): ITable {
				$this->selected = true;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function isSelected(): bool {
				return $this->selected;
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
					$this->where = new WhereGroup($this);
				}
				return $this->where;
			}

			/**
			 * @inheritdoc
			 */
			public function join(IRelation $relation, string $alias): ITable {
				$this->joinList[$alias] = $relation;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function getJoinList(): array {
				return $this->joinList;
			}
		}
