<?php
	declare(strict_types=1);
	namespace Edde\Application;

	use Edde\Factory\InstanceFactory;
	use Edde\Factory\InterfaceFactory;
	use Edde\Log\AbstractLogger;
	use Edde\Log\ILog;
	use Edde\Runtime\IRuntime;
	use Edde\Runtime\RuntimeException;
	use Edde\Service\Application\Application;
	use Edde\Service\Application\RouterService;
	use Edde\Service\Log\LogService;
	use Edde\TestCase;
	use Edde\Url\UrlException;
	use function in_array;

	class ApplicationTest extends TestCase {
		use Application;
		use LogService;
		use RouterService;

		/**
		 * @throws ApplicationException
		 */
		public function testRunException() {
			$this->expectException(RouterException::class);
			$this->expectExceptionMessage('Cannot handle current Cli request.');
			$this->container->registerFactory(new InterfaceFactory(IRuntime::class, DummyRuntime::class));
			$this->logService->registerLogger($logger = new class() extends AbstractLogger {
				public $logs = [];

				public function record(ILog $log, array $tags = []): void {
					if (in_array('exception', $tags)) {
						$logs[] = $log;
					}
				}
			});
			$this->application->run();
			/** @var $logs ILog[] */
			self::assertNotEmpty($logs = $logger->logs);
			[$record] = $logs;
			throw $record->getLog();
		}

		/**
		 * @throws ApplicationException
		 */
		public function testRun() {
			$this->container->registerFactory(new InstanceFactory(IRouterService::class, new TestRouterService('noResponse')));
			self::assertEquals(0, $this->application->run());
		}

		/**
		 * @throws ApplicationException
		 */
		public function testRunControllerException() {
			$this->expectException(ApplicationException::class);
			$this->expectExceptionMessage('Requested class [Edde\Application\SomeService] is not instance of [Edde\Controller\IController].');
			$this->container->registerFactory(new InstanceFactory(IRouterService::class, new TestWrongControllerRouter()));
			$this->application->run();
		}

		/**
		 * @throws ApplicationException
		 * @throws RouterException
		 * @throws RuntimeException
		 * @throws UrlException
		 */
		public function testRunResponse() {
			$this->container->registerFactory(new InstanceFactory(IRouterService::class, new TestRouterService('response')));
			self::assertEquals(123, $this->application->run());
			self::assertSame($this->routerService->createRequest(), $this->routerService->createRequest());
		}
	}
