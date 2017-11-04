<?php
	declare(strict_types=1);
	namespace Edde\Api\Query\Fragment;

		use Edde\Api\Schema\ISchema;

		interface ITable extends IFragment {
			/**
			 * @return ITable
			 */
			public function select(string $alias = null): ITable;

			/**
			 * return selected alias
			 *
			 * @return string
			 */
			public function getSelect(): string;

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
			 * @param string $schema
			 * @param string $alias
			 *
			 * @return ITable
			 */
			public function join(string $schema, string $alias): ITable;

			/**
			 * return list of joined schema names ($alias => $schemaName)
			 *
			 * @return string[]
			 */
			public function getJoinList(): array;
		}
