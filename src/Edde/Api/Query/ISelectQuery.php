<?php
	declare(strict_types=1);
	namespace Edde\Api\Query;

		use Edde\Api\Schema\ISchema;

		interface ISelectQuery extends IQuery {
			/**
			 * @return ISchema
			 */
			public function getSchema(): ISchema;

			/**
			 * @return string
			 */
			public function getAlias(): string;
		}
