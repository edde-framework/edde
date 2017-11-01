<?php
	declare(strict_types=1);
	namespace Edde\Api\Query;

		use Edde\Api\Schema\ISchema;

		interface IInsertQuery extends IQuery {
			/**
			 * target schema of an update
			 *
			 * @return ISchema
			 */
			public function getSchema(): ISchema;

			/**
			 * update data
			 *
			 * @return array
			 */
			public function getSource(): array;
		}
