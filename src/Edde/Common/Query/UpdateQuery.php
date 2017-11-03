<?php
	declare(strict_types=1);
	namespace Edde\Common\Query;

		use Edde\Api\Query\Fragment\ITable;
		use Edde\Api\Query\Fragment\IWhereGroup;
		use Edde\Api\Query\IUpdateQuery;
		use Edde\Api\Schema\ISchema;
		use Edde\Common\Query\Fragment\Table;

		class UpdateQuery extends InsertQuery implements IUpdateQuery {
			/**
			 * @var ITable
			 */
			protected $table;

			public function __construct(ISchema $schema, array $source) {
				parent::__construct($schema, $source);
				$this->type = 'update';
			}

			/**
			 * @inheritdoc
			 */
			public function getTable(): ITable {
				return $this->table ?: $this->table = new Table($this->schema, 'u');
			}

			/**
			 * @inheritdoc
			 */
			public function hasWhere(): bool {
				return $this->table->hasWhere();
			}

			/**
			 * @inheritdoc
			 */
			public function where(): IWhereGroup {
				return $this->getTable()->where();
			}
		}
