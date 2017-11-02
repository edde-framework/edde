<?php
	declare(strict_types=1);
	namespace Edde\Common\Query\Fragment;

		use Edde\Api\Query\Fragment\ISchemaFragment;
		use Edde\Api\Query\Fragment\IWhereGroup;
		use Edde\Api\Query\Fragment\IWhereTo;

		class WhereTo extends WhereExpression implements IWhereTo {
			/**
			 * @inheritdoc
			 */
			public function to($value): IWhereGroup {
				$this->target = 'value';
				$this->value = $value;
				return $this->whereGroup;
			}

			/**
			 * @inheritdoc
			 */
			public function toColumn(string $name, string $prefix) : IWhereGroup {
				$this->target = 'column';
				$this->value = [
					$prefix,
					$name,
				];
				return $this->whereGroup;
			}

			/**
			 * @inheritdoc
			 */
			public function getSchemaFragment(): ISchemaFragment {
				return $this->whereGroup->getSchemaFragment();
			}
		}
