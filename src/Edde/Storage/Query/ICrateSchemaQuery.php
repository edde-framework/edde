<?php
	declare(strict_types=1);
	namespace Edde\Storage\Query;

	use Edde\Query\IQuery;
	use Edde\Schema\ISchema;

	interface ICrateSchemaQuery extends IQuery {
		/**
		 * target schema
		 *
		 * @return ISchema
		 */
		public function getSchema(): ISchema;
	}
