<?php
	declare(strict_types=1);
	namespace Edde\Ext\Container;

		use Edde\Api\Application\IApplication;
		use Edde\Api\Container\Exception\ContainerException;
		use Edde\Api\Container\Exception\FactoryException;
		use Edde\Api\Container\IContainer;
		use Edde\Api\Container\IFactory;
		use Edde\Api\Converter\IConverterManager;
		use Edde\Api\Http\IHttpUtils;
		use Edde\Api\Http\IRequestService as IHttpRequestService;
		use Edde\Api\Log\ILogService;
		use Edde\Api\Protocol\IProtocolService;
		use Edde\Api\Request\IRequestService;
		use Edde\Api\Router\IRouterService;
		use Edde\Api\Runtime\IRuntime;
		use Edde\Api\Utils\ICliUtils;
		use Edde\Api\Utils\IStringUtils;
		use Edde\Api\Xml\IXmlExport;
		use Edde\Api\Xml\IXmlParser;
		use Edde\Common\Application\Application;
		use Edde\Common\Container\Container;
		use Edde\Common\Container\Factory\CallbackFactory;
		use Edde\Common\Container\Factory\ClassFactory;
		use Edde\Common\Container\Factory\ExceptionFactory;
		use Edde\Common\Container\Factory\InstanceFactory;
		use Edde\Common\Container\Factory\InterfaceFactory;
		use Edde\Common\Container\Factory\LinkFactory;
		use Edde\Common\Container\Factory\ProxyFactory;
		use Edde\Common\Converter\ConverterManager;
		use Edde\Common\Http\HttpUtils;
		use Edde\Common\Http\RequestService as HttpRequestService;
		use Edde\Common\Log\LogService;
		use Edde\Common\Object\Object;
		use Edde\Common\Protocol\ProtocolService;
		use Edde\Common\Request\RequestService;
		use Edde\Common\Router\RouterService;
		use Edde\Common\Runtime\Runtime;
		use Edde\Common\Utils\CliUtils;
		use Edde\Common\Utils\StringUtils;
		use Edde\Common\Xml\XmlExport;
		use Edde\Common\Xml\XmlParser;
		use Edde\Ext\Protocol\ProtocolServiceConfigurator;
		use ReflectionMethod;

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
			 * @param array $factoryList
			 *
			 * @return IFactory[]
			 * @throws FactoryException
			 */
			static public function createFactoryList(array $factoryList): array {
				$factories = [];
				foreach ($factoryList as $name => $factory) {
					$current = null;
					if ($factory instanceof \stdClass) {
						switch ($factory->type) {
							case 'instance':
								$current = new InstanceFactory($name, $factory->class, $factory->parameterList, null, $factory->cloneable);
								break;
							case 'exception':
								$current = new ExceptionFactory($name, $factory->class, $factory->message);
								break;
							case 'proxy':
								$current = new ProxyFactory($name, $factory->factory, $factory->method, $factory->parameterList);
								break;
						}
					} else if (is_string($factory) && strpos($factory, '::') !== false) {
						list($target, $method) = explode('::', $factory);
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
						throw new FactoryException(sprintf('Unsupported factory definition [%s; %s].', is_string($name) ? $name : (is_object($name) ? get_class($name) : gettype($name)), is_string($factory) ? $factory : (is_object($factory) ? get_class($factory) : gettype($factory))));
					}
					$factories[$name] = $current;
				}
				return $factories;
			}

			/**
			 * pure way how to simple create a system container
			 *
			 * @param array    $factoryList
			 * @param string[] $configuratorList
			 *
			 * @return IContainer
			 * @throws ContainerException
			 * @throws FactoryException
			 */
			static public function create(array $factoryList = [], array $configuratorList = []): IContainer {
				/** @var $container IContainer */
				$container = new Container();
				/**
				 * this trick ensures that container is properly configured when some internal dependency needs it while container is construction
				 */
				$containerConfigurator = $configuratorList[IContainer::class] = new ContainerConfigurator($factoryList = self::createFactoryList($factoryList), $configuratorList);
				$container->addConfigurator($containerConfigurator);
				$container->setup();
				$container = $container->create(IContainer::class);
				$container->addConfigurator($containerConfigurator);
				$container->setup();
				return $container;
			}

			/**
			 * create a default container with set of services from Edde; they can be simply redefined
			 *
			 * @param array    $factoryList
			 * @param string[] $configuratorList
			 *
			 * @return IContainer
			 * @throws ContainerException
			 * @throws FactoryException
			 */
			static public function container(array $factoryList = [], array $configuratorList = []): IContainer {
				return self::create(array_merge(self::getDefaultFactoryList(), $factoryList), array_filter(array_merge(self::getDefaultConfiguratorList(), $configuratorList)));
			}

			/**
			 * shortcut for autowiring (for example in tests, ...)
			 *
			 * @param mixed $instance
			 * @param array $factoryList
			 * @param array $configuratorList
			 *
			 * @return IContainer
			 * @throws ContainerException
			 * @throws FactoryException
			 */
			static public function inject($instance, array $factoryList = [], array $configuratorList = []): IContainer {
				$container = self::container(empty($factoryList) ? [new ClassFactory()] : $factoryList, $configuratorList);
				$container->inject($instance);
				return $container;
			}

			static public function getDefaultFactoryList(): array {
				return [
					/**
					 * utils
					 */
					IHttpUtils::class          => HttpUtils::class,
					IStringUtils::class        => StringUtils::class,
					ICliUtils::class           => CliUtils::class,
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
					 * The Protocol specification related stuff
					 */
					IProtocolService::class    => ProtocolService::class,
					/**
					 * general service for http request/response
					 */
					IHttpRequestService::class => HttpRequestService::class,
					/**
					 * Xml support
					 */
					IXmlExport::class          => XmlExport::class,
					IXmlParser::class          => XmlParser::class,
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

			static public function getDefaultConfiguratorList(): array {
				return [
					IProtocolService::class => ProtocolServiceConfigurator::class,
				];
			}
		}
