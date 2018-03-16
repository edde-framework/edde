<?php
	declare(strict_types=1);
	namespace Edde\Common\Runtime;

	use Edde\Inject\Runtime\Runtime;
	use Edde\TestCase;

	class RuntimeTest extends TestCase {
		use Runtime;

		public function testIsCli() {
			self::assertTrue($this->runtime->isConsoleMode(), 'runtime is NOT in console mode!');
		}
	}
