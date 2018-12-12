<?php
	declare(strict_types=1);
	use Edde\Schema\UuidSchema;

	interface SomeSchema extends UuidSchema {
		public function date(): DateTime;

		public function bint(): bool;
	}


