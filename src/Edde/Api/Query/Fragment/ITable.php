<?php
	declare(strict_types=1);
	namespace Edde\Api\Query\Fragment;

		use Edde\Api\Schema\IRelation;
		use Edde\Api\Schema\ISchema;

		interface ITable extends IFragment {
			/**
			 * select this schema for data retrieval
			 *
			 * @return ITable
			 */
			public function select(): ITable;

			/**
			 * should be this fragment used as a data source?
			 *
			 * @return bool
			 */
			public function isSelected(): bool;

			/**
			 * get schema of this fragment
			 *
			 * @return ISchema
			 */
			public function getSchema(): ISchema;

			/**
			 * get alias of this schema
			 *
			 * @return string
			 */
			public function getAlias(): string;

			/**
			 * is there a where fragment?
			 *
			 * @return bool
			 */
			public function hasWhere(): bool;

			/**
			 * get the where fragment for this query
			 *
			 * @return IWhereGroup
			 */
			public function where(): IWhereGroup;

			/**
			 * @param IRelation $relation
			 * @param string    $alias
			 *
			 * @return ITable
			 */
			public function join(IRelation $relation, string $alias): ITable;

			/**
			 * return list of relations for this table
			 *
			 * @return IRelation[]
			 */
			public function getJoinList(): array;
		}
