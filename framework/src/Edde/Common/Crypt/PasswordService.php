<?php
	declare(strict_types=1);
	namespace Edde\Common\Crypt;

	use Edde\Api\Crypt\IPasswordService;
	use Edde\Common\Object\Object;

	class PasswordService extends Object implements IPasswordService {
		/** @inheritdoc */
		public function hash(string $password): string {
			return password_hash($password, PASSWORD_DEFAULT);
		}

		/** @inheritdoc */
		public function isValid(string $password, string $hash): bool {
			return password_verify($password, $hash);
		}
	}
