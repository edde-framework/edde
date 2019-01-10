<?php
	declare(strict_types=1);
	namespace Edde\Job;

	use Edde\Schema\UuidSchema;

	interface JobManagerSchema extends UuidSchema {
		const alias = true;

		public function paused(): bool;
	}
