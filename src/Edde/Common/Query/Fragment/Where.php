<?php
	declare(strict_types=1);
	namespace Edde\Common\Query\Fragment;

		use Edde\Api\Query\Fragment\IWhere;
		use Edde\Api\Query\Fragment\IWhereExpression;
		use Edde\Api\Query\Fragment\IWhereGroup;

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
				parent::__construct('where');
				$this->whereGroup = $whereGroup;
				$this->relation = $relation;
			}

			/**
			 * @inheritdoc
			 */
			public function value(string $column, string $expression, $value): IWhereGroup {
				$this->where = new WhereExpression($this->whereGroup, $expression, $column);
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
			public function getExpression(): IWhereExpression {
				return $this->where;
			}
		}
