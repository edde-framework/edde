<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	interface UuidSchema {
		public function uuid($primary): string;
	}
