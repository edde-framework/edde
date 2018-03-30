<?php
	declare(strict_types=1);
	namespace Edde\User;

	use Edde\Access\AccessSchema;
	use Edde\Schema\RelationSchema;

	interface UserAccessSchema extends RelationSchema {
		public function user(): UserSchema;

		public function access(): AccessSchema;
	}
