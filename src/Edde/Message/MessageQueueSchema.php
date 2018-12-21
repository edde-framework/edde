<?php
	declare(strict_types=1);
	namespace Edde\Message;

	use DateTime;
	use Edde\Schema\UuidSchema;

	interface MessageQueueSchema extends UuidSchema {
		/**
		 * when a message should be executed (it could be event later this time)
		 */
		public function stamp(): DateTime;

		/**
		 * actual planned execution time - message should not be processed without
		 * this time set
		 */
		public function executeOn(): ?DateTime;

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
	}
