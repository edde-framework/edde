<?php
	declare(strict_types=1);
	namespace Edde\Common\Query\Fragment;

		use Edde\Api\Query\Fragment\IWhereGroup;
		use Edde\Api\Query\Fragment\IWhereTo;

		class WhereTo extends WhereExpression implements IWhereTo {
			/**
			 * @var string
			 */
			protected $type;
			protected $value;

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
