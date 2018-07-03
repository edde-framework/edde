<?php
	declare(strict_types=1);

	namespace Edde\Common\Container;

	use Edde\Api\Config\IConfigurable;
	use PHPUnit\Framework\TestCase;

	require_once __DIR__ . '/assets/assets.php';

	class ConfigurableTest extends TestCase {
		/**
		 * @var IConfigurable
		 */
		protected $configurable;

		public function testConfigurableInit() {
			$object = new \AnotherSomething();
			self::assertFalse($object->isInitialized());
			self::assertFalse($object->isConfigured());
			self::assertFalse($object->isWarmedup());
			self::assertFalse($object->isSetup());
			self::assertEquals(0, $object->init);
			$object->init();
			self::assertEquals(1, $object->init);
			$object->init();
			self::assertEquals(1, $object->init);
			self::assertTrue($object->isInitialized());
			self::assertFalse($object->isWarmedup());
			self::assertFalse($object->isConfigured());
			self::assertFalse($object->isSetup());
		}

		public function testConfigurableWarmup() {
			$object = new \AnotherSomething();
			self::assertFalse($object->isInitialized());
			self::assertFalse($object->isConfigured());
			self::assertFalse($object->isWarmedup());
			self::assertFalse($object->isSetup());
			self::assertEquals(0, $object->warmup);
			$object->warmup();
			self::assertEquals(1, $object->warmup);
			$object->warmup();
			self::assertEquals(1, $object->warmup);
			self::assertTrue($object->isInitialized());
			self::assertTrue($object->isWarmedup());
			self::assertFalse($object->isConfigured());
			self::assertFalse($object->isSetup());
		}

		public function testConfigurableConfig() {
			$object = new \AnotherSomething();
			self::assertFalse($object->isInitialized());
			self::assertFalse($object->isConfigured());
			self::assertFalse($object->isWarmedup());
			self::assertFalse($object->isSetup());
			self::assertFalse($object->ok);
			self::assertEquals(0, $object->config);
			$object->setConfiguratorList([new \AnotherSomethingConfigurator()]);
			$object->config();
			self::assertEquals(1, $object->config);
			$object->config();
			self::assertEquals(1, $object->config);
			self::assertTrue($object->ok);
			self::assertTrue($object->isInitialized());
			self::assertTrue($object->isWarmedup());
			self::assertTrue($object->isConfigured());
			self::assertFalse($object->isSetup());
		}

		public function testConfigurableSetup() {
			$object = new \AnotherSomething();
			self::assertFalse($object->isInitialized());
			self::assertFalse($object->isWarmedup());
			self::assertFalse($object->isConfigured());
			self::assertFalse($object->isSetup());
			self::assertEquals(0, $object->setup);
			$object->setup();
			self::assertEquals(1, $object->setup);
			$object->setup();
			self::assertEquals(1, $object->setup);
			self::assertTrue($object->isInitialized());
			self::assertTrue($object->isWarmedup());
			self::assertTrue($object->isConfigured());
			self::assertTrue($object->isSetup());
		}

		/**
		 * @codeCoverageIgnore
		 */
		protected function setUp() {
			$this->configurable = new \AnotherSomething();
		}
	}
