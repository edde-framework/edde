<?php
	declare(strict_types=1);
	namespace Edde\Common\Storage\Query\Fragment;

	use Edde\Common\Storage\Query\AbstractFragment;
	use Edde\Storage\Query\Fragment\IWhere;
	use Edde\Storage\Query\Fragment\IWhereGroup;

	class Where extends AbstractFragment implements IWhere {
		/**
		 * @var \Edde\Storage\Query\Fragment\IWhereGroup
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
		public function expression(string $column, string $expression, $value = null): \Edde\Storage\Query\Fragment\IWhereGroup {
			$this->where = [
				$expression,
				'expression',
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
