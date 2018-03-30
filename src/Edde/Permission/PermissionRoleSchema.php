<?php
	declare(strict_types=1);
	namespace Edde\Permission;

	use Edde\Role\RoleSchema;
	use Edde\Schema\RelationSchema;

	interface PermissionRoleSchema extends RelationSchema {
		public function permission(): PermissionSchema;

		public function role(): RoleSchema;
	}
