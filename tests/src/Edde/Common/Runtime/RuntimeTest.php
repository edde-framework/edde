<?php
	declare(strict_types = 1);

	namespace Edde\Common\Runtime;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Runtime\IRuntime;
	use Edde\Ext\Runtime\DefaultSetupHandler;
	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/assets.php');

	class RuntimeTest extends TestCase {
		public function testCommon() {
			$runtime = new Runtime(new SetupHandler());
			self::assertFalse($runtime->isUsed());
			self::assertTrue($runtime->isConsoleMode());
		}

		public function testExecute() {
			$flag = false;
			Runtime::execute(DefaultSetupHandler::create(), function (IRuntime $runtime, IContainer $container) use (&$flag) {
				$flag = true;
				self::assertTrue($runtime->isConsoleMode());
			});
			self::assertTrue($flag);
		}
	}
