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
			protected $relation;
			/**
			 * @var string
			 */
			protected $name;
			protected $value;
			protected $column;

			public function __construct(IWhereFragment $whereFragment, string $relation, string $name) {
				$this->whereFragment = $whereFragment;
				$this->relation = $relation;
				$this->name = $name;
			}

			/**
			 * @inheritdoc
			 */
			public function to($value): IWhereRelation {
				$this->value = $value;
			}

			/**
			 * @inheritdoc
			 */
			public function toColumn(string $name): IWhereRelation {
				$this->column = $name;
			}
		}
