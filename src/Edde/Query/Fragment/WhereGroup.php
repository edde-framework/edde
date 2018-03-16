<?php
	declare(strict_types=1);
	namespace Edde\Query\Fragment;

	use Edde\Query\AbstractFragment;
	use Edde\Storage\Query\Fragment\IWhere;
	use Edde\Storage\Query\Fragment\IWhereGroup;

	class WhereGroup extends AbstractFragment implements IWhereGroup {
		/** @var IWhere[] */
		protected $wheres = [];

		/** @inheritdoc */
		public function and (): IWhere {
			return $this->wheres[] = new Where($this, 'and');
		}

		/** @inheritdoc */
		public function or (): IWhere {
			return $this->wheres[] = new Where($this, 'or');
		}

		/** @inheritdoc */
		public function getIterator() {
			yield from $this->wheres;
		}
	}
