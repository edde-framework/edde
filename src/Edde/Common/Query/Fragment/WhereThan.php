<?php
	declare(strict_types=1);
	namespace Edde\Common\Query\Fragment;

		use Edde\Api\Query\Fragment\IWhereGroup;
		use Edde\Api\Query\Fragment\IWhereThan;

		class WhereThan extends WhereExpression implements IWhereThan {
			/**
			 * @var string
			 */
			protected $type;
			protected $value;

			/**
			 * @inheritdoc
			 */
			public function than($value): IWhereGroup {
				$this->type = 'value';
				$this->value = $value;
				return $this->whereGroup;
			}

			/**
			 * @inheritdoc
			 */
			public function thanColumn(string $name): IWhereGroup {
				$this->type = 'column';
				$this->value = $name;
				return $this->whereGroup;
			}
		}
