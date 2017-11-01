<?php
	declare(strict_types=1);
	namespace Edde\Common\Query\Fragment;

		use Edde\Api\Query\Fragment\IWhereFragment;
		use Edde\Api\Query\Fragment\IWhereRelation;
		use Edde\Api\Query\Fragment\IWhereTo;

		class WhereToFragment extends AbstractFragment implements IWhereTo {
			/**
			 * @var IWhereFragment
			 */
			protected $whereFragment;
			/**
			 * @var string
			 */
			protected $operator;
			/**
			 * @var string
			 */
			protected $name;
			protected $value;
			protected $type;
			/**
			 * @var IWhereRelation
			 */
			protected $relation;

			public function __construct(IWhereFragment $whereFragment, string $operator, string $name) {
				$this->whereFragment = $whereFragment;
				$this->operator = $operator;
				$this->name = $name;
			}

			/**
			 * @inheritdoc
			 */
			public function to($value): IWhereRelation {
				$this->value = $value;
				$this->type = 'value';
				return $this->relation ?: $this->relation = new WhereRelationFragment($this->whereFragment);
			}

			/**
			 * @inheritdoc
			 */
			public function toColumn(string $name): IWhereRelation {
				$this->value = $name;
				$this->type = 'column';
				return $this->relation ?: $this->relation = new WhereRelationFragment($this->whereFragment);
			}
		}
