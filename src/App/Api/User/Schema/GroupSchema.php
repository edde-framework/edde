<?php
	namespace App\Api\User\Schema;

		use App\Api\Schema\GuidSchema;

		interface GroupSchema extends GuidSchema {
			/**
			 * @schema unique
			 */
			public function name(): string;
		}
