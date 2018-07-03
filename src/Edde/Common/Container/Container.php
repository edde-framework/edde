<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Api\Cache\ICache;
	use Edde\Api\Cache\ICacheManager;
	use Edde\Api\Callback\IParameter;
	use Edde\Api\Container\ContainerException;
	use Edde\Api\Container\FactoryException;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IFactory;
	use Edde\Api\Container\IFactoryManager;
	use Edde\Api\Container\ILazyInject;
	use Edde\Common\Container\Factory\FactoryFactory;
	use Edde\Common\Deffered\AbstractDeffered;
	use Edde\Common\Strings\StringUtils;

	/**
	 * Default implementation of a dependency container.
	 */
	class Container extends AbstractDeffered implements IContainer {
		/**
		 * @var IFactoryManager
		 */
		protected $factoryManager;
		/**
		 * @var ICacheManager
		 */
		protected $cacheManager;
		/**
		 * @var ICache
		 */
		protected $cache;
		/**
		 * @var \SplStack
		 */
		protected $dependencyStack;
		protected $dependencyList = [];

		/**
		 * @param IFactoryManager $factoryManager
		 * @param ICacheManager $cacheManager
		 */
		public function __construct(IFactoryManager $factoryManager, ICacheManager $cacheManager) {
			$this->factoryManager = $factoryManager;
			$this->cacheManager = $cacheManager;
		}

		/**
		 * @inheritdoc
		 */
		public function registerFactory(string $name, IFactory $factory): IContainer {
			$this->factoryManager->registerFactory($name, $factory);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function registerFactoryList(array $factoryList): IContainer {
			$this->factoryManager->registerFactoryList($factoryList);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function has(string $name) {
			return $this->factoryManager->hasFactory($name);
		}

		/**
		 * @inheritdoc
		 * @throws ContainerException
		 */
		public function inject($instance) {
			if (is_object($instance) === false) {
				return $instance;
			}
			$this->use();
			$cacheId = ('reflection/' . get_class($instance));
			if ($this->cache === null || ($reflection = $this->cache->load($cacheId)) === null) {
				$reflectionClass = new \ReflectionClass($instance);
				$methodList = [];
				$injectList = [];
				foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
					$name = $reflectionMethod->getName();
					if ($reflectionMethod->getNumberOfParameters() > 0) {
						if (strpos($name, 'inject') !== false && strlen($name) > 6) {
							$methodList[$name] = $name;
						}
						if (strpos($name, 'lazy') !== false && strlen($name) > 4) {
							$parameterList = [];
							foreach ($reflectionMethod->getParameters() as $parameter) {
								if ($reflectionClass->hasProperty($parameter->getName()) === false) {
									throw new ContainerException(vsprintf("Lazy inject mismatch: parameter [$%s] of method [%s::%s()] must have a property [%s::$%s] with the same name as the parameter (for example protected \$%s).", [
										$parameter->getName(),
										$reflectionClass->getName(),
										$reflectionMethod->getName(),
										$reflectionClass->getName(),
										$parameter->getName(),
										$parameter->getName(),
									]));
								}
								$parameterList[$parameter->getName()] = $parameter->getClass()
									->getName();
							}
							$injectList[$name] = $parameterList;
						}
					}
				}
				if (in_array(ILazyInject::class, $reflectionClass->getInterfaceNames(), true) === false) {
					$injectList = [];
				}
				$reflection = [
					'method-list' => $methodList,
					'lazy-inject' => $injectList,
				];
				$this->cache && $this->cache->save($cacheId, $reflection);
			}
			/** @noinspection ForeachSourceInspection */
			/** @var $instance ILazyInject */
			foreach ($reflection['lazy-inject'] as $method) {
				/** @noinspection ForeachSourceInspection */
				foreach ($method as $property => $class) {
					$instance->lazy($property, function () use ($class) {
						return $this->create($class);
					});
				}
			}
			/** @noinspection ForeachSourceInspection */
			foreach ($reflection['method-list'] as $method) {
				$this->call([
					$instance,
					$method,
				]);
			}
			return $instance;
		}

		/**
		 * @inheritdoc
		 * @throws FactoryException
		 * @throws ContainerException
		 */
		public function create(string $name, ...$parameterList) {
			$this->use();
			return $this->factory($this->factoryManager->getFactory($name), $name, $parameterList);
		}

		/**
		 * @inheritdoc
		 * @throws FactoryException
		 * @throws ContainerException
		 */
		public function call(callable $callable, ...$parameterList) {
			$this->use();
			return $this->factory(FactoryFactory::create('', $callable), null, $parameterList);
		}

		/**
		 * @inheritdoc
		 * @throws ContainerException
		 */
		public function factory(IFactory $factory, string $name = null, array $parameterList = []) {
			$this->dependencyStack->push($name = $factory->getName($name));
			if (empty($name) === false && isset($this->dependencyList[$name])) {
//				throw new DependencyException(sprintf('Detected recursive dependency [%s] in stack [%s].', $name, implode(', ', iterator_to_array($this->dependencyStack))));
			}
			$this->dependencyList[$name] = true;
			/** @var $parameters IParameter[] */
			$grab = count($parameters = $factory->getParameterList($name)) - count($parameterList);
			$dependencyList = [];
			foreach ($parameters as $parameter) {
				/** @noinspection NotOptimalIfConditionsInspection */
				if ($grab-- <= 0 || $parameter->isOptional() || (($class = $parameter->getClass()) === null || $this->factoryManager->hasFactory($class) === false) && ($this->factoryManager->hasFactory($class = StringUtils::recamel($parameter->getName())) === false)) {
					break;
				}
				$dependencyList[] = $this->factory($this->factoryManager->getFactory($class), $class);
			}
			try {
				return $factory->create($name, array_merge($dependencyList, $parameterList), $this);
			} finally {
				unset($this->dependencyList[$name]);
				$this->dependencyStack->pop();
			}
		}

		/**
		 * @inheritdoc
		 */
		protected function prepare() {
			$this->dependencyStack = new \SplStack();
			$this->cache = $this->cacheManager->cache(self::class);
		}
	}
