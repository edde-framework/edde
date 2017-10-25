<?php
	namespace App\Api\User\Schema;

		use App\Api\Schema\GuidSchema;

		interface RoleSchema extends GuidSchema {
			/**
			 * @schema unique
			 */
			public function name(): string;
		}
