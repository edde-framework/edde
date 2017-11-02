<?php
	declare(strict_types=1);
	namespace Edde\Api\Query;

		use Edde\Api\Query\Fragment\ISchemaFragment;

		/**
		 * Formal interface for Select Queries (also to map relevant fragments).
		 */
		interface ISelectQuery extends IQuery {
			/**
			 * add a schema to query to be queried
			 *
			 * @param string $schema
			 * @param string $alias
			 *
			 * @return ISchemaFragment
			 */
			public function schema(string $schema, string $alias): ISchemaFragment;

			/**
			 * return list of schemas being queried
			 *
			 * @return ISchemaFragment[]
			 */
			public function getSchemaFragmentList(): array;
		}
