<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Api\Cache\ICacheManager;
	use Edde\Api\Container\FactoryException;
	use Edde\Api\Container\IFactory;
	use Edde\Api\Container\IFactoryManager;
	use Edde\Common\Cache\CacheTrait;
	use Edde\Common\Container\Factory\FactoryFactory;
	use Edde\Common\Deffered\AbstractDeffered;

	/**
	 * Default implementation of a cache manager.
	 */
	class FactoryManager extends AbstractDeffered implements IFactoryManager {
		use CacheTrait;
		/**
		 * @var IFactory[]
		 */
		protected $factoryList = [];
		protected $handleList = [];

		/**
		 * @param ICacheManager $cacheManager
		 */
		public function __construct(ICacheManager $cacheManager) {
			$this->cacheManager = $cacheManager;
		}

		/**
		 * @inheritdoc
		 * @throws FactoryException
		 */
		public function registerFactoryList(array $factoryList): IFactoryManager {
			foreach (FactoryFactory::createList($factoryList) as $name => $factory) {
				$this->registerFactory($name, $factory);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function registerFactory(string $name, IFactory $factory): IFactoryManager {
			$this->factoryList[$name] = $factory;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getFactory(string $name): IFactory {
			$this->use();
			if ($factory = $this->cache->load($cacheId = __FUNCTION__ . $name)) {
				return $this->factoryList[$factory];
			}
			if ($this->hasFactory($name) === false) {
				throw new FactoryException(sprintf('Requested unknown factory [%s].', $name));
			}
			if (isset($this->factoryList[$name])) {
				$factory = $this->factoryList[$name];
				if ($factory->canHandle($name) === false) {
					throw new FactoryException(sprintf('Requested factory cannot handle identifier [%s].', $name));
				}
				$this->cache->save($cacheId, $name);
				return $factory;
			}
			foreach ($this->factoryList as $factoryName => $factory) {
				if ($factory->canHandle($name)) {
					$this->cache->save($cacheId, $factoryName);
					return $factory;
				}
			}
			throw new FactoryException(sprintf('Some strange bug here for factory [%s].', $name));
		}

		/**
		 * @inheritdoc
		 */
		public function hasFactory(string $name): bool {
			$this->use();
			if ($factory = $this->cache->load($cacheId = __FUNCTION__ . $name)) {
				return $factory;
			}
			if (isset($this->factoryList[$name])) {
				return $this->cache->save($cacheId, true);
			}
			foreach ($this->factoryList as $factory) {
				if ($factory->canHandle($name)) {
					return $this->cache->save($cacheId, true);
				}
			}
			return $this->cache->save($cacheId, false);
		}

		/**
		 * @inheritdoc
		 */
		public function getFactoryList(): array {
			return $this->factoryList;
		}

		protected function prepare() {
			parent::prepare();
			$this->cache();
		}
	}
