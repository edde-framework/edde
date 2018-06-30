<?php
	declare(strict_types=1);
	namespace Edde\Factory;

	use Edde\Config\IConfigService;
	use Edde\Container\ContainerException;
	use Edde\Container\IContainer;
	use Edde\Hydrator\IHydratorManager;
	use Edde\Schema\ISchemaManager;
	use Edde\Storage\IStorage;
	use Edde\Storage\PostgresStorage;
	use Edde\TestCase;
	use Edde\Transaction\ITransaction;

	class LinkFactoryTest extends TestCase {
		/**
		 * @throws FactoryException
		 */
		public function testGetReflection() {
			$factory = new LinkFactory(ITransaction::class, IStorage::class);
			$reflection = $factory->getReflection($this->container, IStorage::class);
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
				'Edde\Storage\PostgresStorage',
			], $reflection->getConfigurators());
		}

		/**
		 * @throws FactoryException
		 * @throws ContainerException
		 */
		public function testFactory() {
			$factory = new LinkFactory(ITransaction::class, IStorage::class);
			$reflection = $factory->getReflection($this->container, IStorage::class);
			$instance = $factory->factory($this->container, [], $reflection, PostgresStorage::class);
			self::assertInstanceOf(PostgresStorage::class, $instance);
			self::assertEquals(new Reflection(), $factory->getReflection($this->container, 'nope'));
			self::assertSame($instance, $factory->factory($this->container, [], $reflection, PostgresStorage::class));
		}
	}
