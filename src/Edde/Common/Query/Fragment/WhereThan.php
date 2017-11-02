<?php
	declare(strict_types=1);
	namespace Edde\Common\Query\Fragment;

		use Edde\Api\Query\Fragment\IWhereGroup;
		use Edde\Api\Query\Fragment\IWhereThan;

		class WhereThan extends WhereExpression implements IWhereThan {
			/**
			 * @inheritdoc
			 */
			public function than($value): IWhereGroup {
				$this->target = 'value';
				$this->value = $value;
				return $this->whereGroup;
			}

			/**
			 * @inheritdoc
			 */
			public function thanColumn(string $name, string $prefix) : IWhereGroup {
				$this->target = 'column';
				$this->value = [
					$prefix,
					$name,
				];
				return $this->whereGroup;
			}
		}
