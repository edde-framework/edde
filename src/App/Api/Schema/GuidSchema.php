<?php
	namespace App\Api\Schema;

		interface GuidSchema {
			/**
			 * @schema primary
			 */
			public function guid(): string;
		}
