<?php
	declare(strict_types=1);
	namespace Edde\Access;

	use Edde\Schema\UuidSchema;

	/**
	 * Permission is individual name for an action or resource being commonly
	 * asked (for example "user.read").
	 */
	interface PermissionSchema extends UuidSchema {
		public function name($unique): string;
	}
