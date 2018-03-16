<?php
	declare(strict_types=1);
	namespace Edde\Common\Storage\Query\Fragment;

	use Edde\Common\Storage\Query\AbstractFragment;

	class WhereGroup extends AbstractFragment implements \Edde\Storage\Query\Fragment\IWhereGroup {
		/**
		 * @var \Edde\Storage\Query\Fragment\IWhere[]
		 */
		protected $whereList = [];

		/**
		 * @inheritdoc
		 */
		public function and (): \Edde\Storage\Query\Fragment\IWhere {
			return $this->whereList[] = new Where($this, 'and');
		}

		/**
		 * @inheritdoc
		 */
		public function or (): \Edde\Storage\Query\Fragment\IWhere {
			return $this->whereList[] = new Where($this, 'or');
		}

		/**
		 * @inheritdoc
		 */
		public function getIterator() {
			yield from $this->whereList;
		}
	}
