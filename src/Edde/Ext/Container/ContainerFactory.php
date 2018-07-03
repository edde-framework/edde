<?php
	declare(strict_types=1);

	namespace Edde\Ext\Container;

	use Edde\Api\Acl\IAcl;
	use Edde\Api\Acl\IAclManager;
	use Edde\Api\Application\IApplication;
	use Edde\Api\Application\IContext;
	use Edde\Api\Application\IResponseManager;
	use Edde\Api\Asset\IAssetDirectory;
	use Edde\Api\Asset\IAssetStorage;
	use Edde\Api\Asset\IStorageDirectory;
	use Edde\Api\Cache\ICache;
	use Edde\Api\Cache\ICacheDirectory;
	use Edde\Api\Cache\ICacheManager;
	use Edde\Api\Cache\ICacheStorage;
	use Edde\Api\Container\ContainerException;
	use Edde\Api\Container\FactoryException;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IFactory;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Api\Crate\ICrate;
	use Edde\Api\Crate\ICrateFactory;
	use Edde\Api\Crypt\ICryptEngine;
	use Edde\Api\Database\IDriver;
	use Edde\Api\Database\IDsn;
	use Edde\Api\EddeException;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\File\ITempDirectory;
	use Edde\Api\Html\IHtmlGenerator;
	use Edde\Api\Http\Client\IHttpClient;
	use Edde\Api\Http\IHostUrl;
	use Edde\Api\Http\IHttpRequest;
	use Edde\Api\Http\IHttpResponse;
	use Edde\Api\Identity\IAuthenticatorManager;
	use Edde\Api\Identity\IIdentity;
	use Edde\Api\Identity\IIdentityManager;
	use Edde\Api\Job\IJobManager;
	use Edde\Api\Job\IJobQueue;
	use Edde\Api\Link\ILinkFactory;
	use Edde\Api\Lock\ILockDirectory;
	use Edde\Api\Lock\ILockManager;
	use Edde\Api\Log\ILogDirectory;
	use Edde\Api\Log\ILogService;
	use Edde\Api\Protocol\Event\IEventBus;
	use Edde\Api\Protocol\IElementStore;
	use Edde\Api\Protocol\IProtocolManager;
	use Edde\Api\Protocol\IProtocolService;
	use Edde\Api\Protocol\Request\IRequestService;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Resource\IResourceProvider;
	use Edde\Api\Router\IRouterService;
	use Edde\Api\Runtime\IRuntime;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Api\Session\IFingerprint;
	use Edde\Api\Session\ISessionDirectory;
	use Edde\Api\Session\ISessionManager;
	use Edde\Api\Storage\IStorage;
	use Edde\Api\Store\IStore;
	use Edde\Api\Store\IStoreDirectory;
	use Edde\Api\Store\IStoreManager;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\ITemplateDirectory;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Api\Thread\IExecutor;
	use Edde\Api\Thread\IThreadManager;
	use Edde\Api\Translator\ITranslator;
	use Edde\Api\Upgrade\IUpgradeManager;
	use Edde\Api\Web\IJavaScriptCompiler;
	use Edde\Api\Web\IStyleSheetCompiler;
	use Edde\Api\Xml\IXmlExport;
	use Edde\Api\Xml\IXmlParser;
	use Edde\Common\Acl\Acl;
	use Edde\Common\Acl\AclManager;
	use Edde\Common\Application\Application;
	use Edde\Common\Application\ResponseManager;
	use Edde\Common\Asset\AssetDirectory;
	use Edde\Common\Asset\AssetStorage;
	use Edde\Common\Asset\StorageDirectory;
	use Edde\Common\Cache\Cache;
	use Edde\Common\Cache\CacheDirectory;
	use Edde\Common\Cache\CacheManager;
	use Edde\Common\Container\Container;
	use Edde\Common\Container\Factory\CallbackFactory;
	use Edde\Common\Container\Factory\ClassFactory;
	use Edde\Common\Container\Factory\ExceptionFactory;
	use Edde\Common\Container\Factory\InstanceFactory;
	use Edde\Common\Container\Factory\InterfaceFactory;
	use Edde\Common\Container\Factory\LinkFactory;
	use Edde\Common\Container\Factory\ProxyFactory;
	use Edde\Common\Converter\ConverterManager;
	use Edde\Common\Crate\Crate;
	use Edde\Common\Crate\CrateFactory;
	use Edde\Common\Crypt\CryptEngine;
	use Edde\Common\Database\DatabaseStorage;
	use Edde\Common\File\RootDirectory;
	use Edde\Common\File\TempDirectory;
	use Edde\Common\Html\Html5Generator;
	use Edde\Common\Http\Client\HttpClient;
	use Edde\Common\Http\HostUrl;
	use Edde\Common\Http\HttpRequest;
	use Edde\Common\Http\HttpResponse;
	use Edde\Common\Identity\AuthenticatorManager;
	use Edde\Common\Identity\IdentityManager;
	use Edde\Common\Job\JobManager;
	use Edde\Common\Job\JobQueue;
	use Edde\Common\Lock\FileLockManager;
	use Edde\Common\Lock\LockDirectory;
	use Edde\Common\Log\LogDirectory;
	use Edde\Common\Log\LogService;
	use Edde\Common\Object;
	use Edde\Common\Protocol\ElementStore;
	use Edde\Common\Protocol\Event\EventBus;
	use Edde\Common\Protocol\ProtocolManager;
	use Edde\Common\Protocol\ProtocolService;
	use Edde\Common\Protocol\Request\RequestService;
	use Edde\Common\Resource\ResourceManager;
	use Edde\Common\Router\RouterService;
	use Edde\Common\Runtime\Runtime;
	use Edde\Common\Schema\SchemaManager;
	use Edde\Common\Session\SessionDirectory;
	use Edde\Common\Session\SessionFingerprint;
	use Edde\Common\Session\SessionManager;
	use Edde\Common\Store\StoreDirectory;
	use Edde\Common\Store\StoreManager;
	use Edde\Common\Template\Compiler;
	use Edde\Common\Template\Template;
	use Edde\Common\Template\TemplateDirectory;
	use Edde\Common\Template\TemplateManager;
	use Edde\Common\Thread\ThreadManager;
	use Edde\Common\Thread\WebExecutor;
	use Edde\Common\Translator\Translator;
	use Edde\Common\Upgrade\AbstractUpgradeManager;
	use Edde\Common\Web\JavaScriptCompiler;
	use Edde\Common\Web\StyleSheetCompiler;
	use Edde\Common\Xml\XmlExport;
	use Edde\Common\Xml\XmlParser;
	use Edde\Ext\Cache\FlatFileCacheStorage;
	use Edde\Ext\Cache\InMemoryCacheStorage;
	use Edde\Ext\Converter\ConverterManagerConfigurator;
	use Edde\Ext\Database\Sqlite\SqliteDriver;
	use Edde\Ext\Database\Sqlite\SqliteDsn;
	use Edde\Ext\Job\ThreadManagerConfigurator;
	use Edde\Ext\Link\LinkFactoryConfigurator;
	use Edde\Ext\Log\LogServiceConfigurator;
	use Edde\Ext\Protocol\ProtocolServiceConfigurator;
	use Edde\Ext\Protocol\RequestServiceConfigurator;
	use Edde\Ext\Resource\ResourceManagerConfigurator;
	use Edde\Ext\Router\RouterServiceConfigurator;
	use Edde\Ext\Store\StoreManagerConfigurator;
	use Edde\Ext\Template\CompilerConfigurator;
	use Edde\Ext\Thread\WebExecutorConfigurator;

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
					$reflectionMethod = new \ReflectionMethod($target, $method);
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
					throw new FactoryException(sprintf('Closure is not supported in factory definition [%s].', $name));
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
			/**
			 * A young man and his date were parked on a back road some distance from town.
			 * They were about to have sex when the girl stopped.
			 * “I really should have mentioned this earlier, but I’m actually a hooker and I charge $20 for sex.”
			 * The man reluctantly paid her, and they did their thing.
			 * After a cigarette, the man just sat in the driver’s seat looking out the window.
			 * “Why aren’t we going anywhere?” asked the girl.
			 * “Well, I should have mentioned this before, but I’m actually a taxi driver, and the fare back to town is $25…”
			 */
			/** @var $container IContainer */
			$container = new Container(new Cache(new InMemoryCacheStorage()));
			/**
			 * this trick ensures that container is properly configured when some internal dependency needs it while container is construction
			 */
			$containerConfigurator = $configuratorList[IContainer::class] = new ContainerConfigurator();
			$containerConfigurator->setFactoryList($factoryList = self::createFactoryList($factoryList));
			$containerConfigurator->setConfiguratorList($configuratorList);
			$container->addConfigurator($containerConfigurator);
			$container->setup();
			$container = $container->create(IContainer::class);
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
		 * this magical method tries to guess root directory based on a stack trace
		 *
		 * @param array $factoryList
		 * @param array $configuratorList
		 *
		 * @return IContainer
		 * @throws ContainerException
		 * @throws FactoryException
		 */
		static public function containerWithRoot(array $factoryList = [], array $configuratorList = []): IContainer {
			list(, $trace) = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
			$factoryList[IRootDirectory::class] = $factoryList[IRootDirectory::class] ?? self::instance(RootDirectory::class, [dirname($trace['file'])]);
			return self::container($factoryList, $configuratorList);
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
		static public function autowire($instance, array $factoryList = [], array $configuratorList = []): IContainer {
			$container = self::containerWithRoot(empty($factoryList) ? [new ClassFactory()] : $factoryList, $configuratorList);
			$container->autowire($instance);
			return $container;
		}

		/**
		 * create container and serialize the result into the file; if file exists, container is build from it
		 *
		 * @param array  $factoryList
		 * @param array  $configuratorList
		 * @param string $cacheId
		 *
		 * @return IContainer
		 * @throws ContainerException
		 * @throws FactoryException
		 */
		static public function cache(array $factoryList, array $configuratorList, string $cacheId): IContainer {
			if ($container = @file_get_contents($cacheId)) {
				/** @noinspection UnserializeExploitsInspection */
				return unserialize($container);
			}
			register_shutdown_function(function (IContainer $container, $cache) {
				file_put_contents($cache, serialize($container));
			}, $container = self::container($factoryList, $configuratorList), $cacheId);
			return $container;
		}

		/**
		 * create instance factory
		 *
		 * @param string $class
		 * @param array  $parameterList
		 * @param bool   $cloneable
		 *
		 * @return object
		 */
		static public function instance(string $class, array $parameterList, bool $cloneable = false) {
			return (object)[
				'type'          => __FUNCTION__,
				'class'         => $class,
				'parameterList' => $parameterList,
				'cloneable'     => $cloneable,
			];
		}

		/**
		 * special kind of factory which will thrown an exception of the given message; it's useful for say which internal dependencies are not met
		 *
		 * @param string $message
		|		 * @param string|null $class
		 *
		 * @return object
		 */
		static public function exception(string $message, string $class = null) {
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
		 * @param array  $parameterList
		 *
		 * @return object
		 */
		static public function proxy(string $factory, string $method, array $parameterList) {
			return (object)[
				'type'          => __FUNCTION__,
				'factory'       => $factory,
				'method'        => $method,
				'parameterList' => $parameterList,
			];
		}

		static public function getDefaultFactoryList(): array {
			return [
				IContainer::class         => Container::class,
				IRootDirectory::class     => self::exception(sprintf('Root directory is not specified; please register [%s] interface.', IRootDirectory::class)),
				ITempDirectory::class     => self::proxy(IRootDirectory::class, 'directory', [
					'temp',
					TempDirectory::class,
				]),
				IAssetDirectory::class    => self::proxy(IRootDirectory::class, 'directory', [
					'.assets',
					AssetDirectory::class,
				]),
				ITemplateDirectory::class => self::proxy(IAssetDirectory::class, 'directory', [
					'templates',
					TemplateDirectory::class,
				]),
				ILogDirectory::class      => self::proxy(IRootDirectory::class, 'directory', [
					'logs',
					LogDirectory::class,
				]),

				IStorageDirectory::class => self::proxy(IAssetDirectory::class, 'directory', [
					'storage',
					StorageDirectory::class,
				]),

				/**
				 * Happy cacheing stuff here :)
				 */
				ICacheManager::class     => CacheManager::class,
				ICache::class            => ICacheManager::class,
				ICacheStorage::class     => FlatFileCacheStorage::class,
				ICacheDirectory::class   => self::proxy(ITempDirectory::class, 'directory', [
					'cache',
					CacheDirectory::class,
				]),

				IRuntime::class         => Runtime::class,

				/**
				 * Application request/response stuff
				 */
				IApplication::class     => Application::class,
				IContext::class         => self::exception(sprintf('You have to register implementation of [%s] specific for you application.', IContext::class)),
				IRouterService::class   => RouterService::class,
				IHttpRequest::class     => HttpRequest::class . '::createHttpRequest',
				IHttpResponse::class    => HttpResponse::class . '::createHttpResponse',
				IResponseManager::class => ResponseManager::class,

				IHostUrl::class                       => HostUrl::class . '::createHostUrl',

				/**
				 * Link generation
				 */
				ILinkFactory::class                   => \Edde\Common\Link\LinkFactory::class,

				/**
				 * Support for general content conversion (which also powers server content negotiation)
				 */
				IConverterManager::class              => ConverterManager::class,

				/**
				 * Resource related stuff
				 */
				IResourceManager::class               => ResourceManager::class,
				IResourceProvider::class              => IResourceManager::class,

				/**
				 * Web components related stuff
				 */
				IStyleSheetCompiler::class            => StyleSheetCompiler::class,
				IJavaScriptCompiler::class            => JavaScriptCompiler::class,

				/**
				 * Storage (database) related stuff
				 */
				IStorage::class                       => DatabaseStorage::class,
				IDriver::class                        => SqliteDriver::class,
				IDsn::class                           => self::instance(SqliteDsn::class, ['storage.sqlite']),

				/**
				 * General crate support
				 */
				ICrate::class                         => self::instance(Crate::class, [], true),
				ICrateFactory::class                  => CrateFactory::class,
				ISchemaManager::class                 => SchemaManager::class,

				/**
				 * Http client support
				 */
				IHttpClient::class                    => HttpClient::class,

				/**
				 * General log service
				 */
				ILogService::class                    => LogService::class,

				/**
				 * Custom simple XML parser
				 */
				IXmlParser::class                     => XmlParser::class,

				/**
				 * General support for html generator and template engine
				 */
				IHtmlGenerator::class                 => Html5Generator::class,
				ITemplateManager::class               => TemplateManager::class,
				ITemplate::class                      => self::instance(Template::class, [], true),
				ICompiler::class                      => Compiler::class,

				/**
				 * It's nice when it is possible to upgrade you application...
				 */
				IUpgradeManager::class                => self::exception(sprintf('Upgrade manager is not available; you must register [%s] interface; optionaly default [%s] implementation should help you.', IUpgradeManager::class, AbstractUpgradeManager::class)),

				/**
				 * Simple crypto layer
				 */
				ICryptEngine::class                   => CryptEngine::class,

				/**
				 * Access control, session and Identity (user session)
				 */
				IAclManager::class                    => AclManager::class,
				ISessionManager::class                => SessionManager::class,
				ISessionDirectory::class              => self::proxy(ITempDirectory::class, 'directory', [
					'session',
					SessionDirectory::class,
				]),
				IIdentityManager::class               => IdentityManager::class,
				IIdentity::class                      => IIdentityManager::class,
				IFingerprint::class                   => SessionFingerprint::class,
				IAuthenticatorManager::class          => AuthenticatorManager::class,
				IAclManager::class                    => AclManager::class,
				IAcl::class                           => Acl::class,

				/**
				 * Translation support
				 */
				ITranslator::class                    => Translator::class,

				/**
				 * General asset storage support
				 */
				IAssetStorage::class                  => AssetStorage::class,

				/**
				 * Protocol implementation support
				 */
				IProtocolManager::class               => ProtocolManager::class,
				IProtocolService::class               => ProtocolService::class,
				IRequestService::class                => RequestService::class,
				IEventBus::class                      => EventBus::class,
				\Edde\Ext\Rest\ProtocolService::class => \Edde\Ext\Rest\ProtocolService::class,
				IElementStore::class                  => ElementStore::class,

				/**
				 * Job related implementation
				 */
				IJobManager::class                    => JobManager::class,
				IJobQueue::class                      => JobQueue::class,

				/**
				 * Thread support
				 */
				IThreadManager::class                 => ThreadManager::class,
				IExecutor::class                      => WebExecutor::class,

				/**
				 * Store related stuff
				 */
				IStoreManager::class                  => StoreManager::class,
				IStore::class                         => IStoreManager::class,
				IStoreDirectory::class                => self::proxy(IAssetDirectory::class, 'directory', [
					'store',
					StoreDirectory::class,
				]),

				/**
				 * xml support
				 */
				IXmlExport::class                     => XmlExport::class,

				/**
				 * General Locking support
				 */
				ILockManager::class                   => FileLockManager::class,
				ILockDirectory::class                 => self::proxy(IAssetDirectory::class, 'directory', [
					'.lock',
					LockDirectory::class,
				]),
			];
		}

		static public function getDefaultConfiguratorList(): array {
			return [
				IRouterService::class    => RouterServiceConfigurator::class,
				/**
				 * We are using some custom resource providers, so we have to register them to resource manager and the current
				 * point how to get resources.
				 */
				IResourceManager::class  => ResourceManagerConfigurator::class,
				/**
				 * To enable general content exchange, we have to setup converter manager; it basically allows to do arbitrary
				 * data conversions for example json to array, xml file to INode, ... this component is kind of fundamental part
				 * of the framework.
				 */
				IConverterManager::class => ConverterManagerConfigurator::class,
				/**
				 * As other components, Template engine should be configured too; this will register default set of macros.
				 */
				ICompiler::class         => CompilerConfigurator::class,
				IProtocolService::class  => ProtocolServiceConfigurator::class,
				IRequestService::class   => RequestServiceConfigurator::class,
				ILogService::class       => LogServiceConfigurator::class,
				ILinkFactory::class      => LinkFactoryConfigurator::class,
				WebExecutor::class       => WebExecutorConfigurator::class,
				IThreadManager::class    => ThreadManagerConfigurator::class,
				IStoreManager::class     => StoreManagerConfigurator::class,
			];
		}
	}
