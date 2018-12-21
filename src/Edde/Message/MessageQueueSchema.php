<?php
	declare(strict_types=1);
	namespace Edde\Message;

	use DateTime;
	use Edde\Schema\UuidSchema;

	interface MessageQueueSchema extends UuidSchema {
		public function stamp(): DateTime;

		public function type(): string;

		public function target(): ?string;

		public function message($type = 'json');
	}
