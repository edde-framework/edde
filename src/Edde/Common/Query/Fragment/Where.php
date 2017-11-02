<?php
	declare(strict_types=1);
	namespace Edde\Common\Query\Fragment;

		use Edde\Api\Query\Fragment\IWhere;
		use Edde\Api\Query\Fragment\IWhereExpression;
		use Edde\Api\Query\Fragment\IWhereGroup;
		use Edde\Api\Query\Fragment\IWhereThan;
		use Edde\Api\Query\Fragment\IWhereTo;

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
			 * @var IWhereExpression
			 */
			protected $where;

			public function __construct(IWhereGroup $whereGroup, string $relation) {
				$this->whereGroup = $whereGroup;
				$this->relation = $relation;
			}

			/**
			 * @inheritdoc
			 */
			public function eq(string $name): IWhereTo {
				return $this->where = new WhereTo($this->whereGroup, __FUNCTION__, $name);
			}

			/**
			 * @inheritdoc
			 */
			public function gt(string $name): IWhereThan {
				return $this->where = new WhereThan($this->whereGroup, __FUNCTION__, $name);
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
			public function getExpression(): IWhereExpression {
				return $this->where;
			}
		}
