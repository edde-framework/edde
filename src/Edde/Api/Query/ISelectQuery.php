<?php
	declare(strict_types=1);
	namespace Edde\Api\Query;

		use Edde\Api\Query\Fragment\ITable;
		use Edde\Api\Schema\ISchema;

		/**
		 * Formal interface for Select Queries (also to map relevant fragments).
		 */
		interface ISelectQuery extends IQuery {
			/**
			 * add a schema to query to be queried
			 *
			 * @param ISchema $schema
			 * @param string  $alias
			 *
			 * @return ITable
			 */
			public function table(ISchema $schema, string $alias): ITable;

			/**
			 * return list of schemas being queried
			 *
			 * @return ITable[]
			 */
			public function getTableList(): array;
		}
