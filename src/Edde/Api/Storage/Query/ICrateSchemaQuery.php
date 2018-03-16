<?php
	declare(strict_types=1);
	namespace Edde\Api\Storage\Query;

	use Edde\Schema\ISchema;

	interface ICrateSchemaQuery extends IQuery {
		/**
		 * target schema
		 *
		 * @return \Edde\Schema\ISchema
		 */
		public function getSchema(): ISchema;
	}
