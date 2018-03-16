<?php
	declare(strict_types=1);
	namespace Edde\Ext\Container;

	use Edde\Api\Bus\Event\IEventBus;
	use Edde\Api\Bus\IMessageBus;
	use Edde\Api\Bus\IMessageService;
	use Edde\Api\Bus\Request\IRequestService;
	use Edde\Api\Config\IConfigLoader;
	use Edde\Api\Config\IConfigService;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Api\Crypt\IPasswordService;
	use Edde\Api\Crypt\IRandomService;
	use Edde\Api\Driver\IDriver;
	use Edde\Api\Entity\IEntityManager;
	use Edde\Api\Filter\IFilterManager;
	use Edde\Api\Generator\IGeneratorManager;
	use Edde\Api\Http\IHttpUtils;
	use Edde\Api\Http\IRequestService as IHttpRequestService;
	use Edde\Api\Log\ILogService;
	use Edde\Api\Router\IRouterService;
	use Edde\Api\Runtime\IRuntime;
	use Edde\Api\Sanitizer\ISanitizerManager;
	use Edde\Api\Storage\IStorage;
	use Edde\Api\Upgrade\IUpgradeManager;
	use Edde\Api\Utils\IStringUtils;
	use Edde\Api\Xml\IXmlExport;
	use Edde\Api\Xml\IXmlParser;
	use Edde\Application\Application;
	use Edde\Application\IApplication;
	use Edde\Assets\AssetsDirectory;
	use Edde\Assets\IAssetsDirectory;
	use Edde\Assets\ILogDirectory;
	use Edde\Assets\IRootDirectory;
	use Edde\Assets\ITempDirectory;
	use Edde\Assets\LogDirectory;
	use Edde\Assets\TempDirectory;
	use Edde\Common\Container\Factory\CallbackFactory;
	use Edde\Common\Container\Factory\ClassFactory;
	use Edde\Common\Container\Factory\ExceptionFactory;
	use Edde\Common\Container\Factory\InstanceFactory;
	use Edde\Common\Container\Factory\InterfaceFactory;
	use Edde\Common\Container\Factory\LinkFactory;
	use Edde\Common\Container\Factory\ProxyFactory;
	use Edde\Common\Upgrade\AbstractUpgradeManager;
	use Edde\Configurator\Bus\MessageBusConfigurator;
	use Edde\Configurator\Container\ContainerConfigurator;
	use Edde\Configurator\Converter\ConverterManagerConfigurator;
	use Edde\Configurator\Filter\FilterManagerConfigurator;
	use Edde\Configurator\Generator\GeneratorManagerConfigurator;
	use Edde\Configurator\Router\RouterServiceConfigurator;
	use Edde\Configurator\Sanitizer\SanitizerManagerConfigurator;
	use Edde\Configurator\Schema\SchemaManagerConfigurator;
	use Edde\Configurator\Validator\ValidatorManagerConfigurator;
	use Edde\Container\Container;
	use Edde\Container\ContainerException;
	use Edde\Container\IContainer;
	use Edde\Container\IFactory;
	use Edde\Exception\EddeException;
	use Edde\Object;
	use Edde\Schema\ISchemaManager;
	use Edde\Schema\SchemaManager;
	use Edde\Service\Bus\Event\EventBus;
	use Edde\Service\Bus\MessageBus;
	use Edde\Service\Bus\MessageService;
	use Edde\Service\Bus\Request\RequestService;
	use Edde\Service\Config\ConfigLoader;
	use Edde\Service\Config\ConfigService;
	use Edde\Service\Converter\ConverterManager;
	use Edde\Service\Crypt\PasswordService;
	use Edde\Service\Crypt\RandomService;
	use Edde\Service\Entity\EntityManager;
	use Edde\Service\Filter\FilterManager;
	use Edde\Service\Generator\GeneratorManager;
	use Edde\Service\Http\HttpUtils;
	use Edde\Service\Http\RequestService as HttpRequestService;
	use Edde\Service\Log\LogService;
	use Edde\Service\Router\RouterService;
	use Edde\Service\Runtime\Runtime;
	use Edde\Service\Sanitizer\SanitizerManager;
	use Edde\Service\Storage\Storage;
	use Edde\Service\Utils\StringUtils;
	use Edde\Service\Xml\XmlExport;
	use Edde\Service\Xml\XmlParser;
	use Edde\Validator\IValidatorManager;
	use Edde\Validator\ValidatorManager;
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
				ISchemaManager::class      => SchemaManager::class,
				/**
				 * generator (related to schema) support
				 */
				IGeneratorManager::class   => GeneratorManager::class,
				IFilterManager::class      => FilterManager::class,
				ISanitizerManager::class   => SanitizerManager::class,
				IValidatorManager::class   => ValidatorManager::class,
				/**
				 * random & security support
				 */
				IRandomService::class      => RandomService::class,
				IPasswordService::class    => PasswordService::class,
				/**
				 * storage support
				 */
				IEntityManager::class      => EntityManager::class,
				IStorage::class            => Storage::class,
				IDriver::class             => self::exception(sprintf('Please register driver to use Storage.', IDriver::class)),
				/**
				 * an application upgrades support
				 */
				IUpgradeManager::class     => self::exception(sprintf('You have to provide you own implementation of [%s]; you can use [%s] to get some little help.', IUpgradeManager::class, AbstractUpgradeManager::class)),
				/**
				 * Xml support
				 */
				IXmlExport::class          => XmlExport::class,
				IXmlParser::class          => XmlParser::class,
				/**
				 * Message bus support; probably most important stuff of the
				 * framework and the top killing feature :)
				 */
				IMessageBus::class         => MessageBus::class,
				IMessageService::class     => MessageService::class,
				IEventBus::class           => EventBus::class,
				IRequestService::class     => RequestService::class,
				IConfigService::class      => ConfigService::class,
				IConfigLoader::class       => ConfigLoader::class,
				/**
				 * an application handles lifecycle workflow
				 */
				IApplication::class        => Application::class,
				/**
				 * magical factory for an application execution
				 */
				'application'              => IApplication::class . '::run',
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
