<?php
	declare(strict_types=1);
	namespace Edde\User;

	use Edde\Role\RoleSchema;
	use Edde\Schema\RelationSchema;

	interface UserRoleSchema extends RelationSchema {
		public function user(): UserSchema;

		public function role(): RoleSchema;
	}
