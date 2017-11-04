<?php
	declare(strict_types=1);
	namespace App\Api\Schema;

		interface GuidSchema {
			public function guid($primary): string;
		}
