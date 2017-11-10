<?php
	declare(strict_types=1);
	namespace App\Api\User\Schema;

		use App\Api\Schema\GuidSchema;

		interface UserRoleSchema extends GuidSchema {
			const alias = 'user-role';
			const relation = true;

			public function user(UserSchema $guid): string;

			public function role(RoleSchema $guid): string;
		}
