<?php
	declare(strict_types=1);
	namespace Edde\Common\Query;

		use Edde\Api\Query\Fragment\IWhereGroup;
		use Edde\Api\Query\IUpdateQuery;
		use Edde\Api\Schema\ISchema;
		use Edde\Common\Query\Fragment\WhereGroup;

		class UpdateQuery extends InsertQuery implements IUpdateQuery {
			/**
			 * @var IWhereGroup
			 */
			protected $where;

			public function __construct(ISchema $schema, array $source) {
				parent::__construct($schema, $source);
				$this->type = 'update';
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
		}
