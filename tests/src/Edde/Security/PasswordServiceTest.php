<?php
	declare(strict_types=1);
	namespace Edde\Security;

	use Edde\Service\Security\PasswordService;
	use Edde\TestCase;

	class PasswordServiceTest extends TestCase {
		use PasswordService;

		public function testPasswordService() {
			self::assertTrue($this->passwordService->isValid('1234', $this->passwordService->hash('1234')));
		}
	}
