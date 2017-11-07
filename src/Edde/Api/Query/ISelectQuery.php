<?php
	declare(strict_types=1);
	namespace Edde\Api\Query;

		use Edde\Api\Entity\IEntity;
		use Edde\Api\Query\Fragment\ITable;
		use Edde\Api\Schema\ILink;

		interface ISelectQuery extends IQuery {
			/**
			 * make a link using the given entity
			 *
			 * @param IEntity $entity
			 * @param ILink   $link
			 *
			 * @return ISelectQuery
			 */
			public function link(IEntity $entity, ILink $link): ISelectQuery;

			/**
			 * join the given schema the previously joined schema
			 *
			 * @param string $schema
			 * @param string $alias
			 *
			 * @return ISelectQuery
			 */
			public function join(string $schema, string $alias): ISelectQuery;

			/**
			 * which source should be selected for the output (alias must exists)
			 *
			 * @param string $alias
			 *
			 * @return ISelectQuery
			 */
			public function select(string $alias = null): ISelectQuery;

			/**
			 * shorthand for where and ($name $relation $value); by default it takes last
			 * added alias
			 *
			 * @param string $name
			 * @param string $relation
			 * @param mixed  $value
			 *
			 * @return ISelectQuery
			 */
			public function where(string $name, string $relation, $value): ISelectQuery;

			/**
			 * @param string $name
			 * @param bool   $asc
			 *
			 * @return ISelectQuery
			 */
			public function order(string $name, bool $asc = true): ISelectQuery;

			/**
			 * get base table of this query
			 *
			 * @return ITable
			 */
			public function getTable(): ITable;
		}
