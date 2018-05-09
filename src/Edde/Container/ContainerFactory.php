<?php
	declare(strict_types=1);
	namespace Edde\Container;

	use Edde\Application\Application;
	use Edde\Application\IApplication;
	use Edde\Collection\CollectionManager;
	use Edde\Collection\EntityManager;
	use Edde\Collection\ICollectionManager;
	use Edde\Collection\IEntityManager;
	use Edde\Config\ConfigLoader;
	use Edde\Config\ConfigService;
	use Edde\Config\IConfigLoader;
	use Edde\Config\IConfigService;
	use Edde\Container\Factory\CallbackFactory;
	use Edde\Container\Factory\ClassFactory;
	use Edde\Container\Factory\ExceptionFactory;
	use Edde\Container\Factory\InstanceFactory;
	use Edde\Container\Factory\InterfaceFactory;
	use Edde\Container\Factory\LinkFactory;
	use Edde\Container\Factory\ProxyFactory;
	use Edde\Edde;
	use Edde\EddeException;
	use Edde\Filter\FilterManager;
	use Edde\Filter\FilterManagerConfigurator;
	use Edde\Filter\IFilterManager;
	use Edde\Http\HttpUtils;
	use Edde\Http\IHttpUtils;
	use Edde\Http\IRequestService as IHttpRequestService;
	use Edde\Http\RequestService as HttpRequestService;
	use Edde\Log\ILogService;
	use Edde\Log\LogService;
	use Edde\Router\IRouterService;
	use Edde\Router\RouterService;
	use Edde\Router\RouterServiceConfigurator;
	use Edde\Runtime\IRuntime;
	use Edde\Runtime\Runtime;
	use Edde\Schema\ISchemaFilterService;
	use Edde\Schema\ISchemaLoader;
	use Edde\Schema\ISchemaManager;
	use Edde\Schema\ISchemaValidatorService;
	use Edde\Schema\SchemaFilterService;
	use Edde\Schema\SchemaManager;
	use Edde\Schema\SchemaReflectionLoader;
	use Edde\Schema\SchemaValidatorService;
	use Edde\Security\IPasswordService;
	use Edde\Security\IRandomService;
	use Edde\Security\PasswordService;
	use Edde\Security\RandomService;
	use Edde\Storage\IStorage;
	use Edde\Storage\ITransaction;
	use Edde\Storage\MysqlStorage;
	use Edde\Upgrade\IUpgradeManager;
	use Edde\Upgrade\UpgradeManager;
	use Edde\Utils\IStringUtils;
	use Edde\Utils\StringUtils;
	use Edde\Validator\IValidatorManager;
	use Edde\Validator\ValidatorManager;
	use Edde\Validator\ValidatorManagerConfigurator;
	use Edde\Xml\IXmlExportService;
	use Edde\Xml\IXmlParserService;
	use Edde\Xml\XmlExportService;
	use Edde\Xml\XmlParserService;
	use ReflectionException;
	use ReflectionMethod;
	use stdClass;

	/**
	 * A young man and his date were parked on a back road some distance from town.
	 * They were about to have sex when the girl stopped.
	 * “I really should have mentioned this earlier, but I’m actually a hooker and I charge $20 for sex.”
	 * The man reluctantly paid her, and they did their thing.
	 * After a cigarette, the man just sat in the driver’s seat looking out the window.
	 * “Why aren’t we going anywhere?” asked the girl.
	 * “Well, I should have mentioned this before, but I’m actually a taxi driver, and the fare back to town is $25…”
	 */
	class ContainerFactory extends Edde {
		/**
		 * for the integration purposes
		 *
		 * @var IContainer
		 */
		static protected $instance;

		/**
		 * @return IContainer
		 * @throws ContainerException
		 */
		static public function getContainer(): IContainer {
			if (self::$instance) {
				return self::$instance;
			}
			throw new ContainerException(sprintf('No Container is available; please use some factory method of [%s] to create a container.', static::class));
		}

		/**
		 * @param array $factories
		 *
		 * @return IFactory[]
		 *
		 * @throws ContainerException
		 * @throws ReflectionException
		 */
		static public function createFactories(array $factories): array {
			$instances = [];
			foreach ($factories as $name => $factory) {
				$current = null;
				if ($factory instanceof stdClass) {
					switch ($factory->type) {
						case 'instance':
							$current = new InstanceFactory($name, $factory->class, $factory->params, null, $factory->cloneable);
							break;
						case 'exception':
							$current = new ExceptionFactory($name, $factory->class, $factory->message);
							break;
						case 'proxy':
							$current = new ProxyFactory($name, $factory->factory, $factory->method, $factory->params);
							break;
					}
				} else if (is_string($factory) && strpos($factory, '::') !== false) {
					[$target, $method] = explode('::', $factory);
					$reflectionMethod = new ReflectionMethod($target, $method);
					$current = new ProxyFactory($name, $target, $method);
					if ($reflectionMethod->isStatic()) {
						$current = new CallbackFactory($factory, $name);
					}
				} else if (is_string($name) && is_string($factory) && (interface_exists($factory) || class_exists($factory))) {
					if (class_exists($factory)) {
						$current = new InterfaceFactory($name, $factory);
					} else if (interface_exists($factory)) {
						$current = new LinkFactory($name, $factory);
					}
				} else if ($factory instanceof IFactory) {
					$current = $factory;
				} else if (is_callable($factory)) {
					$current = new CallbackFactory($factory, $name);
				}
				if ($current === null) {
					throw new ContainerException(sprintf('Unsupported factory definition [%s; %s].', is_string($name) ? $name : (is_object($name) ? get_class($name) : gettype($name)), is_string($factory) ? $factory : (is_object($factory) ? get_class($factory) : gettype($factory))));
				}
				$instances[$name] = $current;
			}
			return $instances;
		}

		/**
		 * create instance factory
		 *
		 * @param string $class
		 * @param array  $params
		 * @param bool   $cloneable
		 *
		 * @return stdClass
		 */
		static public function instance(string $class, array $params, bool $cloneable = false): stdClass {
			return (object)[
				'type'      => __FUNCTION__,
				'class'     => $class,
				'params'    => $params,
				'cloneable' => $cloneable,
			];
		}

		/**
		 * special kind of factory which will thrown an exception of the given message; it's useful for say which internal dependencies are not met
		 *
		 * @param string      $message
		 * @param string|null $class
		 *
		 * @return stdClass
		 */
		static public function exception(string $message, string $class = null): stdClass {
			return (object)[
				'type'    => __FUNCTION__,
				'message' => $message,
				'class'   => $class ?: EddeException::class,
			];
		}

		/**
		 * create proxy call factory
		 *
		 * @param string $factory
		 * @param string $method
		 * @param array  $params
		 *
		 * @return stdClass
		 */
		static public function proxy(string $factory, string $method, array $params): stdClass {
			return (object)[
				'type'    => __FUNCTION__,
				'factory' => $factory,
				'method'  => $method,
				'params'  => $params,
			];
		}

		/**
		 * pure way how to simple create a system container
		 *
		 * @param array    $factories
		 * @param string[] $configurators
		 *
		 * @return IContainer
		 *
		 * @throws ContainerException
		 * @throws ReflectionException
		 */
		static public function create(array $factories = [], array $configurators = []): IContainer {
			/**
			 * this trick ensures that container is properly configured when some internal dependency needs it while container is construction
			 */
			$containerConfigurator = $configurators[IContainer::class] = new ContainerConfigurator($factories = self::createFactories($factories), $configurators);
			($container = new Container())->addConfigurator($containerConfigurator);
			$container->setup();
			$container = $container->create(IContainer::class);
			$container->addConfigurator($containerConfigurator);
			$container->setup();
			return self::$instance = $container;
		}

		/**
		 * create a default container with set of services from Edde; they can be simply redefined
		 *
		 * @param array    $factories
		 * @param string[] $configurators
		 *
		 * @return IContainer
		 *
		 * @throws ContainerException
		 * @throws ReflectionException
		 */
		static public function container(array $factories = [], array $configurators = []): IContainer {
			return self::create(array_merge(self::getDefaultFactories(), $factories), array_filter(array_merge(self::getDefaultConfigurators(), $configurators)));
		}

		/**
		 * shortcut for autowiring (for example in tests, ...)
		 *
		 * @param mixed $instance
		 * @param array $factories
		 * @param array $configurators
		 *
		 * @return IContainer
		 *
		 * @throws ContainerException
		 * @throws ReflectionException
		 */
		static public function inject($instance, array $factories = [], array $configurators = []): IContainer {
			$container = self::container(empty($factories) ? [new ClassFactory()] : $factories, $configurators);
			$container->inject($instance);
			return $container;
		}

		static public function getDefaultFactories(): array {
			return [
				/**
				 * utils
				 */
				IHttpUtils::class              => HttpUtils::class,
				IStringUtils::class            => StringUtils::class,
				/**
				 * container implementation
				 */
				IContainer::class              => Container::class,
				/**
				 * runtime info provider
				 */
				IRuntime::class                => Runtime::class,
				/**
				 * log support
				 */
				ILogService::class             => LogService::class,
				/**
				 * user request into protocol element translation
				 */
				IRouterService::class          => RouterService::class,
				/**
				 * general service for http request/response
				 */
				IHttpRequestService::class     => HttpRequestService::class,
				/**
				 * schema support
				 */
				ISchemaManager::class          => SchemaManager::class,
				ISchemaLoader::class           => SchemaReflectionLoader::class,
				ISchemaFilterService::class    => SchemaFilterService::class,
				ISchemaValidatorService::class => SchemaValidatorService::class,
				/**
				 * validation support
				 */
				IValidatorManager::class       => ValidatorManager::class,
				/**
				 * random & security support
				 */
				IRandomService::class          => RandomService::class,
				IPasswordService::class        => PasswordService::class,
				/**
				 * storage support
				 */
				IEntityManager::class          => EntityManager::class,
				IStorage::class                => MysqlStorage::class,
				ITransaction::class            => IStorage::class,
				ICollectionManager::class      => CollectionManager::class,
				/**
				 * general filtering (data conversion) support
				 */
				IFilterManager::class          => FilterManager::class,
				/**
				 * an application upgrades support
				 */
				IUpgradeManager::class         => UpgradeManager::class,
				/**
				 * Xml support
				 */
				IXmlExportService::class       => XmlExportService::class,
				IXmlParserService::class       => XmlParserService::class,
				/**
				 * simple scalar configuration support (should not be used
				 * for any complex config as it's considered to be anti-pattern)
				 */
				IConfigService::class          => ConfigService::class,
				IConfigLoader::class           => ConfigLoader::class,
				/**
				 * an application handles lifecycle workflow
				 */
				IApplication::class            => Application::class,
				/**
				 * magical factory for an application execution
				 */
				'application'                  => IApplication::class . '::run',
			];
		}

		static public function getDefaultConfigurators(): array {
			return [
				IRouterService::class    => RouterServiceConfigurator::class,
				IFilterManager::class    => FilterManagerConfigurator::class,
				IValidatorManager::class => ValidatorManagerConfigurator::class,
			];
		}
	}
