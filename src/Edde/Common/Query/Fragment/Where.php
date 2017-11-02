<?php
	declare(strict_types=1);
	namespace Edde\Common\Query\Fragment;

		use Edde\Api\Query\Fragment\IWhere;
		use Edde\Api\Query\Fragment\IWhereGroup;
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
			 * @var IWhereTo
			 */
			protected $whereTo;

			public function __construct(IWhereGroup $whereGroup, string $relation) {
				$this->whereGroup = $whereGroup;
				$this->relation = $relation;
			}

			/**
			 * @inheritdoc
			 */
			public function eq(string $name): IWhereTo {
				return $this->whereTo = new WhereTo($this->whereGroup, __FUNCTION__, $name);
			}
		}
