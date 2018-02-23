<?php
	declare(strict_types=1);
	namespace Edde\Common\Storage\Query\Fragment;

	use Edde\Api\Storage\Query\Fragment\IWhere;
	use Edde\Api\Storage\Query\Fragment\IWhereGroup;
	use Edde\Common\Storage\Query\AbstractFragment;

	class Where extends AbstractFragment implements IWhere {
		/**
		 * @var IWhereGroup
		 */
		protected $whereGroup;
		/**
		 * @var string
		 */
		protected $relation;
		/**
		 * @var array
		 */
		protected $where;

		public function __construct(IWhereGroup $whereGroup, string $relation) {
			$this->whereGroup = $whereGroup;
			$this->relation = $relation;
		}

		/**
		 * @inheritdoc
		 */
		public function value(string $column, string $operator, $value): IWhereGroup {
			$this->where = [
				$operator,
				'value',
				$column,
				$value,
			];
			return $this->whereGroup;
		}

		/**
		 * @inheritdoc
		 */
		public function getRelation(): string {
			return $this->relation;
		}

		/**
		 * @inheritdoc
		 */
		public function getWhere(): array {
			return $this->where;
		}
	}
