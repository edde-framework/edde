<?php
	declare(strict_types=1);
	namespace Edde\Api\Crypt;

	use Edde\Api\Config\IConfigurable;

	interface IPasswordService extends IConfigurable {
		/**
		 * hash the given string as a password
		 *
		 * @param string $password
		 *
		 * @return string
		 */
		public function hash(string $password): string;

		/**
		 * is the given password same with the given hash?
		 *
		 * @param string $password
		 * @param string $hash
		 *
		 * @return bool
		 */
		public function isValid(string $password, string $hash): bool;
	}
