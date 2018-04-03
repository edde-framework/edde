<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	interface UuidSchema {
		const primary = 'uuid';

		public function uuid(): string;
	}
