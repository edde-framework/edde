<?php
	declare(strict_types=1);
	namespace Edde\Api\Entity\Query;

	use Edde\Api\Entity\IEntity;
	use Edde\Api\Storage\Query\Fragment\IWhereGroup;
	use Edde\Api\Storage\Query\IQuery;
	use Edde\Schema\IRelation;

	interface IDisconnectQuery extends IQuery {
		/**
		 * @return IEntity
		 */
		public function getEntity(): IEntity;

		/**
		 * @return \Edde\Schema\IRelation
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
		 * @return IWhereGroup
		 */
		public function getWhere(): IWhereGroup;
	}
