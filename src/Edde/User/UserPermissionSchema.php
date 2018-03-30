<?php
	declare(strict_types=1);
	namespace Edde\User;

	use Edde\Permission\PermissionSchema;
	use Edde\Schema\RelationSchema;

	interface UserPermissionSchema extends RelationSchema {
		public function user(): UserSchema;

		public function permission(): PermissionSchema;
	}
