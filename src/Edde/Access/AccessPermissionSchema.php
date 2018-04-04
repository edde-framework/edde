<?php
	declare(strict_types=1);
	namespace Edde\Access;

	use Edde\Permission\PermissionSchema;
	use Edde\Schema\UuidSchema;

	interface AccessPermissionSchema extends UuidSchema {
		public function access(): AccessSchema;

		public function permission(): PermissionSchema;
	}
