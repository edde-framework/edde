<?php
	declare(strict_types=1);
	namespace Edde\Application;

	use Edde\Configurable\AbstractConfigurator;
	use Edde\Container\ContainerException;
	use Edde\Log\AbstractLogger;
	use Edde\Log\ILog;
	use Edde\Router\IRouterService;
	use Edde\Router\RouterException;
	use Edde\Service\Application\Application;
	use Edde\Service\Container\Container;
	use Edde\Service\Log\LogService;
	use Edde\Service\Router\RouterService;
	use Edde\TestCase;
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
			$this->expectExceptionMessage('Cannot handle current request.');
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
		 * @throws ContainerException
		 */
		public function testRun() {
			$this->container->registerConfigurator(IRouterService::class, $this->container->inject(new class() extends AbstractConfigurator {
				use Container;

				/**
				 * @param $instance IRouterService
				 */
				public function configure($instance) {
					parent::configure($instance);
					$instance->registerRouter($this->container->inject(new TestRouter('noResponse')));
				}
			}));
			self::assertEquals(0, $this->application->run());
		}

		/**
		 * @throws ApplicationException
		 * @throws ContainerException
		 */
		public function testRunControllerException() {
			$this->expectException(ApplicationException::class);
			$this->expectExceptionMessage('Requested controller [Edde\Application\SomeService] is not instance of [Edde\Controller\IController].');
			$this->container->registerConfigurator(IRouterService::class, $this->container->inject(new class() extends AbstractConfigurator {
				use Container;

				/**
				 * @param $instance IRouterService
				 */
				public function configure($instance) {
					parent::configure($instance);
					$instance->registerRouter($this->container->inject(new TestWrongControllerRouter()));
				}
			}));
			$this->application->run();
		}

		/**
		 * @throws ApplicationException
		 * @throws ContainerException
		 * @throws RouterException
		 */
		public function testRunResponse() {
			$this->container->registerConfigurator(IRouterService::class, $this->container->inject(new class() extends AbstractConfigurator {
				use Container;

				/**
				 * @param $instance IRouterService
				 */
				public function configure($instance) {
					parent::configure($instance);
					$instance->registerRouter($this->container->inject(new TestRouter('response')));
				}
			}));
			self::assertEquals(123, $this->application->run());
			self::assertSame($this->routerService->createRequest(), $this->routerService->createRequest());
		}
	}
