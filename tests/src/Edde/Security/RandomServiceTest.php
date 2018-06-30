<?php
	declare(strict_types=1);
	namespace Edde\Security;

	use Edde\Service\Security\RandomService;
	use Edde\TestCase;

	class RandomServiceTest extends TestCase {
		use RandomService;

		public function testUuid() {
			self::assertSame('66376662-6261-4665-b036-333666383930', $this->randomService->uuid('foo'));
		}

		public function testGenerate() {
			self::assertRegExp('~^[0-9a-z]{10}$~', $this->randomService->generate(10));
		}
	}
