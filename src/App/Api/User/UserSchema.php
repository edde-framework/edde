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
		}
