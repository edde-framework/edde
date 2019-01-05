<?php
	declare(strict_types=1);
	namespace Edde\Job;

	use DateTime;
	use Edde\Schema\UuidSchema;

	interface JobSchema extends UuidSchema {
		const STATE_CREATED = 0;
		const STATE_ENQUEUED = 1;
		const STATE_RUNNING = 2;
		const STATE_DONE = 3;
		const STATE_FAILED = 4;

		public function state($default = self::STATE_CREATED): int;

		/**
		 * when a message should be executed
		 */
		public function stamp(): DateTime;

		/**
		 * message itself
		 */
		public function message($type = 'json');
	}
