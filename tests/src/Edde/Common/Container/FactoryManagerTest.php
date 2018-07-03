<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Api\Container\FactoryException;
	use Edde\Api\Container\IFactoryManager;
	use Edde\Common\Cache\DummyCacheManager;
	use Edde\Common\Container\Factory\InstanceFactory;
	use phpunit\framework\TestCase;

	/**
	 * Test suite for FactoryManager.
	 */
	class FactoryManagerTest extends TestCase {
		/**
		 * @var IFactoryManager
		 */
		protected $factoryManager;

		public function testCommon() {
			$this->factoryManager->registerFactory('foo', new InstanceFactory('foo', $this));
			self::assertTrue($this->factoryManager->hasFactory('foo'));
		}

		public function testException() {
			$this->expectException(FactoryException::class);
			$this->expectExceptionMessage('Requested unknown factory [poo].');
			$this->factoryManager->getFactory('poo');
		}

		protected function setUp() {
			$this->factoryManager = new FactoryManager(new DummyCacheManager());
		}
	}
