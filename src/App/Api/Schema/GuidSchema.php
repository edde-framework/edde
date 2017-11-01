<?php
	declare(strict_types=1);
	namespace App\Api\Schema;

		interface GuidSchema {
			/**
			 * @schema primary
			 */
			public function guid(): string;
		}
