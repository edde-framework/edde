<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\Entity\IEntity;
	use Edde\Schema\IRelation;
	use Edde\Storage\Query\Fragment\IWhereGroup;

	interface IDisconnectQuery extends IQuery {
		/**
		 * @return IEntity
		 */
		public function getEntity(): IEntity;

		/**
		 * @return IRelation
		 */
		public function getRelation(): IRelation;

		/**
		 * shorthand for where and ($name $relation $value); by default it takes last
		 * added alias
		 *
		 * @param string $name
		 * @param string $relation
		 * @param mixed  $value
		 *
		 * @return IDisconnectQuery
		 */
		public function where(string $name, string $relation, $value): IDisconnectQuery;

		/**
		 * @return bool
		 */
		public function hasWhere(): bool;

		/**
		 * @return \Edde\Storage\Query\Fragment\IWhereGroup
		 */
		public function getWhere(): IWhereGroup;
	}
