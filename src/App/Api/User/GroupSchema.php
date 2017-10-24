<?php
	namespace App\Api\User;

		interface GroupSchema {
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
			public function userGroup(): UserSchema;
		}
