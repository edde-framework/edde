<?php
	declare(strict_types=1);
	namespace Edde\Common\Query\Fragment;

		use Edde\Api\Query\Fragment\IWhereGroup;
		use Edde\Api\Query\Fragment\IWhereTo;

		class WhereTo extends AbstractFragment implements IWhereTo {
			/**
			 * @var IWhereGroup
			 */
			protected $whereGroup;
			/**
			 * @var string
			 */
			protected $operator;
			/**
			 * @var string
			 */
			protected $name;
			/**
			 * @var string
			 */
			protected $type;
			protected $value;

			public function __construct(IWhereGroup $whereGroup, string $operator, string $name) {
				$this->whereGroup = $whereGroup;
				$this->operator = $operator;
				$this->name = $name;
			}

			/**
			 * @inheritdoc
			 */
			public function to($value): IWhereGroup {
				$this->type = 'value';
				$this->value = $value;
				return $this->whereGroup;
			}

			/**
			 * @inheritdoc
			 */
			public function toColumn(string $name): IWhereGroup {
				$this->type = 'column';
				$this->value = $name;
				return $this->whereGroup;
			}
		}
