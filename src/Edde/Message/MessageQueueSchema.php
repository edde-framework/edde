<?php
	declare(strict_types=1);
	namespace Edde\Message;

	use DateTime;
	use Edde\Schema\UuidSchema;

	interface MessageQueueSchema extends UuidSchema {
		const STATE_CREATED = 0;
		const STATE_ENQUEUED = 1;
		const STATE_RUNNING = 2;
		const STATE_DONE = 3;

		public function state($default = self::STATE_CREATED): int;

		/**
		 * when messages are enqueued, they're closed into batch for execution
		 */
		public function batch(): string;

		/**
		 * when a message should be executed (it could be event later this time)
		 */
		public function stamp(): DateTime;

		/**
		 * message type from specification
		 */
		public function type(): string;

		/**
		 * optional message target from specification
		 */
		public function target(): ?string;

		/**
		 * message itself
		 */
		public function message($type = 'json');

		/**
		 * amount of time this message has taken
		 */
		public function runtime(): ?float;
	}
