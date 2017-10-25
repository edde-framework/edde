<?php
	namespace App\Api\User;

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
