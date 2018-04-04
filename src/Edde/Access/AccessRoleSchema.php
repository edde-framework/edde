<?php
	declare(strict_types=1);
	namespace Edde\Access;

	use Edde\Role\RoleSchema;
	use Edde\Schema\UuidSchema;

	interface AccessRoleSchema extends UuidSchema {
		public function access(): AccessSchema;

		public function role(): RoleSchema;
	}
