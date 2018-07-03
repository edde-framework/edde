<?php
	declare(strict_types = 1);

	namespace Edde\Common\Runtime;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Runtime\RuntimeException;
	use Edde\Ext\Runtime\DefaultSetupHandler;
	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/assets.php');

	class SetupHandlerTest extends TestCase {
		public function testCommon() {
			self::assertInstanceOf(IContainer::class, DefaultSetupHandler::create()
				->createContainer());
		}

		public function testMultirun() {
			$this->expectException(RuntimeException::class);
			$this->expectExceptionMessage('Cannot run [Edde\Common\Runtime\SetupHandler::createContainer()] multiple times; something is wrong!');
			$setupHandler = DefaultSetupHandler::create();
			$setupHandler->createContainer();
			$setupHandler->createContainer();
		}
	}
