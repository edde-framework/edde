<?php
	declare(strict_types=1);
	namespace Edde\User;

	use Edde\Access\AccessSchema;
	use Edde\Schema\UuidSchema;

	interface UserAccessSchema extends UuidSchema {
		public function user(): UserSchema;

		public function access(): AccessSchema;
	}
