<?php
	declare(strict_types=1);
	namespace App\Api\User\Schema;

		interface UserGroupSchema {
			const alias = 'user-group';
			const relation = true;

			public function user(UserSchema $guid): string;

			public function group(GroupSchema $guid): string;
		}
