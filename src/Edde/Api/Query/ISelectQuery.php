<?php
	declare(strict_types=1);
	namespace Edde\Api\Query;

		use Edde\Api\Query\Fragment\ISchemaFragment;
		use Edde\Api\Schema\ISchema;

		/**
		 * Formal interface for Select Queries (also to map relevant fragments).
		 */
		interface ISelectQuery extends IQuery {
			/**
			 * add a schema to query to be queried
			 *
			 * @param ISchema     $schema
			 * @param string|null $alias
			 *
			 * @return ISchemaFragment
			 */
			public function schema(ISchema $schema, string $alias = null): ISchemaFragment;

			/**
			 * return list of schemas being queried
			 *
			 * @return ISchema[]
			 */
			public function getSchemaFragmentList(): array;
		}
