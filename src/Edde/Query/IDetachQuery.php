<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\Entity\IEntity;
	use Edde\Schema\IRelation;
	use Edde\Storage\Query\Fragment\IWhereGroup;

	interface IDetachQuery extends IQuery {
		/**
		 * @return IEntity
		 */
		public function getEntity(): IEntity;

		/**
		 * @return IEntity
		 */
		public function getTarget(): IEntity;

		/**
		 * @return IRelation
		 */
		public function getRelation(): IRelation;

		/**
		 * @param string $name
		 * @param string $relation
		 * @param        $value
		 *
		 * @return IDetachQuery
		 */
		public function where(string $name, string $relation, $value): IDetachQuery;

		/**
		 * @return bool
		 */
		public function hasWhere(): bool;

		/**
		 * @return \Edde\Storage\Query\Fragment\IWhereGroup
		 */
		public function getWhere(): IWhereGroup;
	}
