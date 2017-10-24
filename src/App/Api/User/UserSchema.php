<?php
	namespace App\Api\User;

	/**
	 * Special kind of interface defining a schema for a class of objects.
	 */
		interface UserSchema {
			/**
			 * @schema primary
			 */
			public function guid(): string;

			/**
			 * user's password could be null as it means user has disabled account (login)
			 *
			 * @schema
			 */
			public function password(): ?string;

			/**
			 * relation means M:N relation; if relation table does not exists (related schema), one
			 * is automagically created
			 *
			 * @schema
			 * @relation guid
			 */
			public function userRole(): RoleSchema;

			/**
			 * @schema
			 * @relation guid
			 */
			public function userGroup(): GroupSchema;
		}
