<?php
	declare(strict_types=1);
	namespace App\Api\User\Schema;

	/**
	 * Relations should have explicit schemas to keep them clear and
	 * eliminated that heavy magic behind it.
	 */
		interface UserGroupSchema {
			/**
			 * @schema primary link
			 */
			public function user(): UserSchema;

			/**
			 * @schema primary link
			 */
			public function group(): GroupSchema;
		}
