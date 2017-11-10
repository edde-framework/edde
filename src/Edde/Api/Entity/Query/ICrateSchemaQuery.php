<?php
	declare(strict_types=1);
	namespace Edde\Api\Entity\Query;

		use Edde\Api\Schema\ISchema;

		interface ICrateSchemaQuery extends IQuery {
			/**
			 * target schema
			 *
			 * @return ISchema
			 */
			public function getSchema(): ISchema;
		}
