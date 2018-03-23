<?php
	declare(strict_types=1);
	namespace Edde\Access;

	use Edde\Schema\RelationSchema;

	interface PrivilegePermissionSchema extends RelationSchema {
		public function permission(PermissionSchema $uuid): string;

		public function privilege(PrivilegeSchema $uuid): string;
	}
