<?php
	declare(strict_types=1);
	namespace Edde\Runtime;

	use Edde\Service\Runtime\Runtime;
	use Edde\TestCase;

	class RuntimeTest extends TestCase {
		use Runtime;

		public function testIsCli() {
			self::assertTrue($this->runtime->isConsoleMode(), 'runtime is NOT in console mode!');
		}
	}
