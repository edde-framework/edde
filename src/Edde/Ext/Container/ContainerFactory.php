<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Container;

	use Edde\Api\Cache\ICacheManager;
	use Edde\Api\Cache\ICacheStorage;
	use Edde\Api\Container\ContainerException;
	use Edde\Api\Container\FactoryException;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IFactoryManager;
	use Edde\Common\AbstractObject;
	use Edde\Common\Cache\CacheManager;
	use Edde\Common\Container\Container;
	use Edde\Common\Container\Factory\ClassFactory;
	use Edde\Common\Container\FactoryManager;
	use Edde\Ext\Cache\InMemoryCacheStorage;

	/**
	 * Simple cache for "handy" container creation.
	 */
	class ContainerFactory extends AbstractObject {
		/**
		 * simple cache method for default (and simle) container instance
		 *
		 * @param array $factoryList
		 *
		 * @return IContainer
		 * @throws ContainerException
		 * @throws FactoryException
		 */
		static public function create(array $factoryList = []): IContainer {
			if (isset($factoryList[ICacheManager::class]) && is_object($factoryList[ICacheManager::class]) === false) {
				throw new ContainerException(sprintf('[%s] must be instance (special case).', ICacheManager::class));
			}
			if (isset($factoryList[ICacheStorage::class]) && is_object($factoryList[ICacheStorage::class]) === false) {
				throw new ContainerException(sprintf('[%s] must be instance (special case).', ICacheStorage::class));
			}
			$factoryManager = new FactoryManager($cacheManager = $factoryList[ICacheManager::class] ?? new CacheManager($factoryList[ICacheStorage::class] ?? new InMemoryCacheStorage()));
			$factoryManager->registerFactoryList($factoryList);
			$container = new Container($factoryManager, $cacheManager);
			$factoryManager->registerFactoryList([
				IContainer::class => $container,
				IFactoryManager::class => $factoryManager,
				ICacheManager::class => $cacheManager,
				new ClassFactory(),
			]);
			return $container;
		}

		/**
		 * create simple independent container with the given cache definition
		 *
		 * @param array $factoryList
		 *
		 * @return IContainer
		 * @throws FactoryException
		 */
		static public function simple(array $factoryList = []): IContainer {
			$factoryManager = new FactoryManager($cacheManager = new CacheManager(new InMemoryCacheStorage()));
			$factoryManager->registerFactoryList($factoryList);
			return new Container($factoryManager, $cacheManager);
		}
	}
