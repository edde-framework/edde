<?php
	declare(strict_types=1);
	namespace Edde\Common\Query;

		use Edde\Api\Query\Fragment\IWhereFragment;
		use Edde\Api\Query\IUpdateQuery;
		use Edde\Api\Schema\ISchema;
		use Edde\Common\Query\Fragment\SchemaFragment;
		use Edde\Common\Query\Fragment\WhereFragment;

		class UpdateQuery extends InsertQuery implements IUpdateQuery {
			/**
			 * @var IWhereFragment
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
			public function where(): IWhereFragment {
				if ($this->where === null) {
					$this->where = new WhereFragment(new SchemaFragment($this->schema, 'u'));
				}
				return $this->where;
			}
		}
