<?php
	declare(strict_types=1);
	namespace Edde\Security;

	use Edde\TestCase;

	class PasswordServiceTest extends TestCase {
		use Edde\Service\Security\PasswordService;

		public function testPasswordService() {
			self::assertTrue($this->passwordService->isValid('1234', $this->passwordService->hash('1234')));
		}
	}
