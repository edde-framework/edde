<?php
	declare(strict_types=1);
	namespace Edde\Access;

	use Edde\Role\RoleSchema;
	use Edde\Schema\RelationSchema;

	interface AccessRoleSchema extends RelationSchema {
		public function access(): AccessSchema;

		public function role(): RoleSchema;
	}
