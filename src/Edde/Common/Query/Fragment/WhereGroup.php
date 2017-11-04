<?php
	declare(strict_types=1);
	namespace Edde\Common\Query\Fragment;

		use Edde\Api\Query\Fragment\IWhere;
		use Edde\Api\Query\Fragment\IWhereGroup;

		class WhereGroup extends AbstractFragment implements IWhereGroup {
			/**
			 * @var IWhere[]
			 */
			protected $whereList = [];

			/**
			 * @inheritdoc
			 */
			public function and (): IWhere {
				return $this->whereList[] = new Where($this, 'and');
			}

			/**
			 * @inheritdoc
			 */
			public function or (): IWhere {
				return $this->whereList[] = new Where($this, 'or');
			}

			/**
			 * @inheritdoc
			 */
			public function getIterator() {
				yield from $this->whereList;
			}
		}
