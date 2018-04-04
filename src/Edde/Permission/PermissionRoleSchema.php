<?php
	declare(strict_types=1);
	namespace Edde\Permission;

	use Edde\Role\RoleSchema;
	use Edde\Schema\UuidSchema;

	interface PermissionRoleSchema extends UuidSchema {
		public function permission(): PermissionSchema;

		public function role(): RoleSchema;
	}
