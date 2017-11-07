<?php
	declare(strict_types=1);
	namespace Edde\Api\Schema;

		interface IRelation extends ILink {
			/**
			 * return relation schema
			 *
			 * @return ISchema
			 */
			public function getSchema(): ISchema;
		}
