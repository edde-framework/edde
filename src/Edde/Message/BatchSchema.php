<?php
	declare(strict_types=1);
	namespace Edde\Message;

	use Edde\Schema\UuidSchema;

	interface BatchSchema extends UuidSchema {
		const STATE_CREATED = 0;
		const STATE_RUNNING = 1;
		const STATE_DONE = 2;

		public function state($default = self::STATE_CREATED): int;

		/**
		 * batch uuid (messages should be linked to this one)
		 */
		public function batch($unique): string;

		/**
		 * overall runtime of this batch
		 */
		public function runtime(): ?float;
	}
