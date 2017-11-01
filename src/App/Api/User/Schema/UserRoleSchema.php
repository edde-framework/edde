<?php
	declare(strict_types=1);
	namespace App\Api\User\Schema;

		interface UserRoleSchema {
			/**
			 * @schema primary link
			 */
			public function user(): UserSchema;

			/**
			 * @schema primary link
			 */
			public function role(): RoleSchema;
		}
