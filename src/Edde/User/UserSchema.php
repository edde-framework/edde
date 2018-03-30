<?php
	declare(strict_types=1);
	namespace Edde\User;

	use DateTime;
	use Edde\Schema\UuidSchema;

	interface UserSchema extends UuidSchema {
		public function name($unique): string;

		public function email($unique): string;

		public function password(): string;

		public function nick(): ?string;

		public function token($unique): ?string;

		public function created($generator = 'stamp'): DateTime;
	}
