<?php
	declare(strict_types=1);
	namespace App\Api\User\Schema;

		use App\Api\Schema\GuidSchema;

		interface RoleSchema extends GuidSchema {
			const alias = 'role';

			public function name($unique): string;
		}
