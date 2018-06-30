<?php
	declare(strict_types=1);
	namespace Edde\Factory;

	use Edde\Config\IConfigService;
	use Edde\Container\ContainerException;
	use Edde\Container\IContainer;
	use Edde\Hydrator\IHydratorManager;
	use Edde\Schema\ISchemaManager;
	use Edde\Storage\MysqlStorage;
	use Edde\TestCase;

	class CascadeFactoryTest extends TestCase {
		public function testCanHandle() {
			$factory = new CascadeFactory();
			self::assertTrue($factory->canHandle($this->container, 'Container\\Container'));
		}

		public function testCannotHandle() {
			$factory = new CascadeFactory();
			self::assertFalse($factory->canHandle($this->container, 'Nope'));
		}

		/**
		 * @throws FactoryException
		 */
		public function testGetReflection() {
			$factory = new CascadeFactory();
			self::assertEquals(['Edde'], $factory->discover(null));
			/**
			 * this is here just to heat coverage
			 */
			$factory->getReflection($this->container, 'Storage\\MysqlStorage');
			$reflection = $factory->getReflection($this->container, 'Storage\\MysqlStorage');
			self::assertEquals([
				new Parameter('configService', IConfigService::class),
				new Parameter('hydratorManager', IHydratorManager::class),
				new Parameter('schemaManager', ISchemaManager::class),
				new Parameter('container', IContainer::class),
			], $reflection->getInjects());
			self::assertEquals([
				'Edde\Storage\IStorage',
				'Edde\Configurable\IConfigurable',
				'Edde\Container\IAutowire',
				'Edde\Transaction\ITransaction',
				'Edde\Storage\MysqlStorage',
			], $reflection->getConfigurators());
		}

		/**
		 * @throws ContainerException
		 * @throws FactoryException
		 */
		public function testFactory() {
			$factory = new CascadeFactory();
			$reflection = $factory->getReflection($this->container, 'Storage\\MysqlStorage');
			$instance = $factory->factory($this->container, [], $reflection, 'Storage\\MysqlStorage');
			self::assertInstanceOf(MysqlStorage::class, $instance);
		}
	}
