<?php
	declare(strict_types=1);
	namespace Edde\Access;

	use Edde\Permission\PermissionSchema;
	use Edde\Schema\RelationSchema;

	interface AccessPermissionSchema extends RelationSchema {
		public function access(): AccessSchema;

		public function permission(): PermissionSchema;
	}
