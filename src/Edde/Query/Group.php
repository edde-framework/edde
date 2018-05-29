<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\SimpleObject;

	class Group extends SimpleObject implements IGroup {
		/**
		 * @var IWhere[]
		 */
		protected $wheres = [];

		/**
		 * @param IWhere[] $wheres
		 */
		public function __construct(array $wheres) {
			$this->wheres = $wheres;
		}
	}
