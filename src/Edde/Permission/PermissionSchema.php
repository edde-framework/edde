<?php
	declare(strict_types=1);
	namespace Edde\Permission;

	use Edde\Schema\UuidSchema;

	interface PermissionSchema extends UuidSchema {
		public function name($unique): string;
	}
