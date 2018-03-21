<?php
	declare(strict_types=1);
	namespace Edde\Container;

	use Edde\Application\Application;
	use Edde\Application\IApplication;
	use Edde\Assets\AssetsDirectory;
	use Edde\Assets\IAssetsDirectory;
	use Edde\Assets\ILogDirectory;
	use Edde\Assets\IRootDirectory;
	use Edde\Assets\ITempDirectory;
	use Edde\Assets\LogDirectory;
	use Edde\Assets\TempDirectory;
	use Edde\Bus\EventBus;
	use Edde\Bus\IEventBus;
	use Edde\Bus\IMessageBus;
	use Edde\Bus\IMessageService;
	use Edde\Bus\IRequestService;
	use Edde\Bus\MessageBus;
	use Edde\Bus\MessageService;
	use Edde\Bus\RequestService;
	use Edde\Config\ConfigLoader;
	use Edde\Config\ConfigService;
	use Edde\Config\IConfigLoader;
	use Edde\Config\IConfigService;
	use Edde\Configurator\Bus\MessageBusConfigurator;
	use Edde\Configurator\Container\ContainerConfigurator;
	use Edde\Configurator\Converter\ConverterManagerConfigurator;
	use Edde\Configurator\Filter\FilterManagerConfigurator;
	use Edde\Configurator\Generator\GeneratorManagerConfigurator;
	use Edde\Configurator\Router\RouterServiceConfigurator;
	use Edde\Configurator\Sanitizer\SanitizerManagerConfigurator;
	use Edde\Configurator\Schema\SchemaManagerConfigurator;
	use Edde\Configurator\Validator\ValidatorManagerConfigurator;
	use Edde\Connection\IConnection;
	use Edde\Container\Factory\CallbackFactory;
	use Edde\Container\Factory\ClassFactory;
	use Edde\Container\Factory\ExceptionFactory;
	use Edde\Container\Factory\InstanceFactory;
	use Edde\Container\Factory\InterfaceFactory;
	use Edde\Container\Factory\LinkFactory;
	use Edde\Container\Factory\ProxyFactory;
	use Edde\Converter\ConverterManager;
	use Edde\Converter\IConverterManager;
	use Edde\Crypt\IPasswordService;
	use Edde\Crypt\IRandomService;
	use Edde\Crypt\PasswordService;
	use Edde\Crypt\RandomService;
	use Edde\EddeException;
	use Edde\Entity\EntityManager;
	use Edde\Entity\IEntityManager;
	use Edde\Filter\FilterManager;
	use Edde\Filter\IFilterManager;
	use Edde\Generator\GeneratorManager;
	use Edde\Generator\IGeneratorManager;
	use Edde\Http\HttpUtils;
	use Edde\Http\IHttpUtils;
	use Edde\Http\IRequestService as IHttpRequestService;
	use Edde\Http\RequestService as HttpRequestService;
	use Edde\Log\ILogService;
	use Edde\Log\LogService;
	use Edde\Object;
	use Edde\Router\IRouterService;
	use Edde\Router\RouterService;
	use Edde\Runtime\IRuntime;
	use Edde\Runtime\Runtime;
	use Edde\Sanitizer\ISanitizerManager;
	use Edde\Sanitizer\SanitizerManager;
	use Edde\Schema\ISchemaManager;
	use Edde\Schema\SchemaManager;
	use Edde\Storage\IStorage;
	use Edde\Storage\Storage;
	use Edde\Upgrade\AbstractUpgradeManager;
	use Edde\Upgrade\IUpgradeManager;
	use Edde\Utils\IStringUtils;
	use Edde\Utils\StringUtils;
	use Edde\Validator\IValidatorManager;
	use Edde\Validator\ValidatorManager;
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
	class ContainerFactory extends Object {
		/**
		 * for the integration purposes
		 *
		 * @var IContainer
		 */
		static protected $instance;

		/**
		 * @return IContainer
		 * @throws \Edde\Container\ContainerException
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
				} else if ($factory instanceof \Edde\Container\IFactory) {
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
			$container = new Container();
			/**
			 * this trick ensures that container is properly configured when some internal dependency needs it while container is construction
			 */
			$containerConfigurator = $configurators[IContainer::class] = new ContainerConfigurator($factories = self::createFactories($factories), $configurators);
			$container->addConfigurator($containerConfigurator);
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
				IRootDirectory::class      => self::exception(sprintf('Root directory is not specified; please register [%s] interface.', IRootDirectory::class)),
				IAssetsDirectory::class    => self::proxy(IRootDirectory::class, 'directory', [
					'.assets',
					AssetsDirectory::class,
				]),
				ITempDirectory::class      => self::proxy(IAssetsDirectory::class, 'directory', [
					'temp',
					TempDirectory::class,
				]),
				ILogDirectory::class       => self::proxy(IAssetsDirectory::class, 'directory', [
					'logs',
					LogDirectory::class,
				]),
				/**
				 * utils
				 */
				IHttpUtils::class          => HttpUtils::class,
				IStringUtils::class        => StringUtils::class,
				/**
				 * container implementation
				 */
				IContainer::class          => Container::class,
				/**
				 * runtime info provider
				 */
				IRuntime::class            => Runtime::class,
				/**
				 * log support
				 */
				ILogService::class         => LogService::class,
				/**
				 * user request into protocol element translation
				 */
				IRouterService::class      => RouterService::class,
				IRequestService::class     => RequestService::class,
				/**
				 * content conversion implementation (mainly useful for server content
				 * negotiation)
				 */
				IConverterManager::class   => ConverterManager::class,
				/**
				 * general service for http request/response
				 */
				IHttpRequestService::class => HttpRequestService::class,
				/**
				 * schema support
				 */
				ISchemaManager::class    => SchemaManager::class,
				/**
				 * generator (related to schema) support
				 */
				IGeneratorManager::class => GeneratorManager::class,
				IFilterManager::class    => FilterManager::class,
				ISanitizerManager::class => SanitizerManager::class,
				IValidatorManager::class => ValidatorManager::class,
				/**
				 * random & security support
				 */
				IRandomService::class    => RandomService::class,
				IPasswordService::class  => PasswordService::class,
				/**
				 * storage support
				 */
				IEntityManager::class    => EntityManager::class,
				IStorage::class          => Storage::class,
				IConnection::class       => self::exception(sprintf('Please register driver to use Storage.', IConnection::class)),
				/**
				 * an application upgrades support
				 */
				IUpgradeManager::class   => self::exception(sprintf('You have to provide you own implementation of [%s]; you can use [%s] to get some little help.', IUpgradeManager::class, AbstractUpgradeManager::class)),
				/**
				 * Xml support
				 */
				IXmlExportService::class => XmlExportService::class,
				IXmlParserService::class => XmlParserService::class,
				/**
				 * Message bus support; probably most important stuff of the
				 * framework and the top killing feature :)
				 */
				IMessageBus::class       => MessageBus::class,
				IMessageService::class   => MessageService::class,
				IEventBus::class         => EventBus::class,
				IRequestService::class   => RequestService::class,
				IConfigService::class    => ConfigService::class,
				IConfigLoader::class     => ConfigLoader::class,
				/**
				 * an application handles lifecycle workflow
				 */
				IApplication::class      => Application::class,
				/**
				 * magical factory for an application execution
				 */
				'application'            => IApplication::class . '::run',
			];
		}

		static public function getDefaultConfigurators(): array {
			return [
				IRouterService::class    => RouterServiceConfigurator::class,
				IConverterManager::class => ConverterManagerConfigurator::class,
				IGeneratorManager::class => GeneratorManagerConfigurator::class,
				IFilterManager::class    => FilterManagerConfigurator::class,
				ISanitizerManager::class => SanitizerManagerConfigurator::class,
				IValidatorManager::class => ValidatorManagerConfigurator::class,
				ISchemaManager::class    => SchemaManagerConfigurator::class,
				IMessageBus::class       => MessageBusConfigurator::class,
			];
		}
	}
