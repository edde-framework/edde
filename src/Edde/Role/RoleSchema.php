<?php
	declare(strict_types=1);
	namespace Edde\Role;

	use Edde\Schema\UuidSchema;

	interface RoleSchema extends UuidSchema {
		public function name($unique): string;
	}
