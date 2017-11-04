<?php
	declare(strict_types=1);
	namespace App\Api\Upgrade;

		use App\Api\Schema\GuidSchema;

		interface UpgradeSchema extends GuidSchema {
			const alias = 'upgrade';

			public function version($unique): string;

			public function stamp(): \DateTime;
		}
