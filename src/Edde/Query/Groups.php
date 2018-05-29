<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\SimpleObject;

	class Groups extends SimpleObject implements IGroups {
		/** @var IWheres */
		protected $wheres;
		/** @var IGroup[] */
		protected $groups = [];

		/**
		 * @param IWheres $wheres
		 */
		public function __construct(IWheres $wheres) {
			$this->wheres = $wheres;
		}

		/** @inheritdoc */
		public function group(string $name = null): IGroup {
			if ($this->hasGroup($name)) {
				throw new QueryException(sprintf('Group [%s] already exists in group list.', $name));
			}
			return $this->groups[$name] = new Group();
		}

		/** @inheritdoc */
		public function hasGroup(?string $name): bool {
			return isset($this->groups[$name]);
		}
	}
