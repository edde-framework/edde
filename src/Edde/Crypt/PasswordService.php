<?php
	declare(strict_types=1);
	namespace Edde\Crypt;

	use Edde\Edde;

	class PasswordService extends Edde implements IPasswordService {
		/** @inheritdoc */
		public function hash(string $password): string {
			return password_hash($password, PASSWORD_DEFAULT);
		}

		/** @inheritdoc */
		public function isValid(string $password, string $hash): bool {
			return password_verify($password, $hash);
		}
	}
