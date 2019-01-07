<?php
	declare(strict_types=1);
	namespace Edde\Job;

	use DateTime;
	use Edde\Schema\UuidSchema;

	interface JobSchema extends UuidSchema {
		const alias = true;

		/**
		 * when a message should be executed
		 */
		public function stamp(): DateTime;

		/**
		 * packet to be processed
		 */
		public function packet($type = 'json');
	}
