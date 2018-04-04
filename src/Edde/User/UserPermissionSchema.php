<?php
	declare(strict_types=1);
	namespace Edde\User;

	use Edde\Permission\PermissionSchema;
	use Edde\Schema\UuidSchema;

	interface UserPermissionSchema extends UuidSchema {
		public function user(): UserSchema;

		public function permission(): PermissionSchema;
	}
