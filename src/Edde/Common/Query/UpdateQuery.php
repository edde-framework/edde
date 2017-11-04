<?php
	declare(strict_types=1);
	namespace Edde\Common\Query;

		use Edde\Api\Query\Fragment\ITable;
		use Edde\Api\Query\Fragment\IWhereGroup;
		use Edde\Api\Query\IUpdateQuery;
		use Edde\Common\Query\Fragment\Table;

		class UpdateQuery extends InsertQuery implements IUpdateQuery {
			/**
			 * @var ITable
			 */
			protected $table;

			/**
			 * @inheritdoc
			 */
			public function getTable(): ITable {
				$this->init();
				return $this->table ?: $this->table = new Table($this->schema, 'u');
			}

			/**
			 * @inheritdoc
			 */
			public function hasWhere(): bool {
				$this->init();
				return $this->table->hasWhere();
			}

			/**
			 * @inheritdoc
			 */
			public function where(): IWhereGroup {
				$this->init();
				return $this->getTable()->where();
			}

			protected function handleInit(): void {
				parent::handleInit();
				if ($this->schema->hasPrimary()) {
					$this->where()->and()->eq($name = $this->schema->getPrimary()->getName())->to($this->source[$name]);
				}
			}
		}
