<?php
	namespace App\Api\User;

		interface RoleSchema {
			/**
			 * @schema primary
			 */
			public function guid(): string;

			/**
			 * @schema unique
			 */
			public function name(): string;

			/**
			 * @relation guid
			 */
			public function UserRole(): UserSchema;
		}
