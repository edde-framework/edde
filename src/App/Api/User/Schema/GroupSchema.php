<?php
	declare(strict_types=1);
	namespace App\Api\User\Schema;

		use App\Api\Schema\GuidSchema;

		interface GroupSchema extends GuidSchema {
			const alias = 'group';

			public function name($unique): string;
		}
