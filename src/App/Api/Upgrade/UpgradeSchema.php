<?php
	declare(strict_types=1);
	namespace App\Api\Upgrade;

		interface UpgradeSchema {
			/**
			 * @schema primary
			 */
			public function guid(): string;

			/**
			 * @schema unique
			 */
			public function version(): string;

			/**
			 * @schema
			 */
			public function stamp(): \DateTime;
		}
