<?php
	declare(strict_types=1);
	namespace Edde\Router;

	use Edde\Service\Router\RouterService;
	use Edde\TestCase;

	class RouterServiceTest extends TestCase {
		use RouterService;

		public function testCanHandle() {
			self::assertFalse($this->routerService->canHandle());
		}
	}
