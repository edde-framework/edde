<?php
	namespace Edde\Api\Query;

		use Edde\Api\Schema\ISchema;

		interface ICrateSchemaQuery extends IQuery {
			/**
			 * target schema
			 *
			 * @return ISchema
			 */
			public function getSchema(): ISchema;
		}
