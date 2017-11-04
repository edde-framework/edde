<?php
	declare(strict_types=1);
	namespace App\Api\User\Schema;

		use App\Api\Schema\GuidSchema;

		interface UserSchema extends GuidSchema {
			const alias = 'user';

			/**
			 * user's password could be null as it means user has disabled account (login)
			 */
			public function password(): ?string;
		}
