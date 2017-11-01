<?php
	declare(strict_types=1);
	namespace Edde\Common\Query\Fragment;

		use Edde\Api\Query\Fragment\IWhereRelation;
		use Edde\Api\Query\Fragment\IWhereTo;

		class WhereToFragment extends AbstractFragment implements IWhereTo {
			/**
			 * @inheritdoc
			 */
			public function to($value): IWhereRelation {
				$this->node->setAttribute('target', 'parameter');
				$this->node->setAttribute('parameter', $value);
				return new WhereRelationFragment($this->root, $this->node);
			}

			/**
			 * @inheritdoc
			 */
			public function toColumn(string $name, string $prefix = null): IWhereRelation {
				$this->node->setAttribute('target', 'column');
				$this->node->setAttribute('column', $name);
				$this->node->setAttribute('column-prefix', $prefix);
				return new WhereRelationFragment($this->root, $this->node);
			}
		}
