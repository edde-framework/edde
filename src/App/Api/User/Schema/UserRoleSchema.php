<?php
	declare(strict_types=1);
	namespace App\Api\User\Schema;

		interface UserRoleSchema {
			const alias = 'user-role';
			const relation = true;

			public function user(UserSchema $guid): string;

			public function role(RoleSchema $guid): string;
		}
