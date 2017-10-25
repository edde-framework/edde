<?php
	namespace App\Api\User\Schema;

		use App\Api\Schema\GuidSchema;

		interface UserSchema extends GuidSchema {
			/**
			 * user's password could be null as it means user has disabled account (login)
			 *
			 * @schema
			 */
			public function password(): ?string;
		}
