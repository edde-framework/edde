<?php
	declare(strict_types=1);
	namespace Edde\User;

	use Edde\Role\RoleSchema;
	use Edde\Schema\UuidSchema;

	interface UserRoleSchema extends UuidSchema {
		public function user(): UserSchema;

		public function role(): RoleSchema;
	}
