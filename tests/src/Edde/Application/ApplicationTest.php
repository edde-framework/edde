<?php
	declare(strict_types=1);
	namespace Edde\Application;

	use Edde\Configurable\AbstractConfigurator;
	use Edde\Container\ContainerException;
	use Edde\Log\ILogRecord;
	use Edde\Log\SimpleLog;
	use Edde\Router\IRouterService;
	use Edde\Router\RouterException;
	use Edde\Service\Application\Application;
	use Edde\Service\Container\Container;
	use Edde\Service\Log\LogService;
	use Edde\TestCase;

	class ApplicationTest extends TestCase {
		use Application;
		use LogService;

		/**
		 * @throws ApplicationException
		 */
		public function testRunException() {
			$this->expectException(RouterException::class);
			$this->expectExceptionMessage('Cannot handle current request.');
			$this->logService->registerLog($log = new SimpleLog(), ['exception']);
			$this->application->run();
			/** @var $logs ILogRecord[] */
			self::assertNotEmpty($logs = $log->getLogRecords());
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
					$instance->registerRouter($this->container->create(TestRouter::class, ['noResponse']));
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
		 */
		public function testRunResponse() {
			$this->container->registerConfigurator(IRouterService::class, $this->container->inject(new class() extends AbstractConfigurator {
				use Container;

				/**
				 * @param $instance IRouterService
				 */
				public function configure($instance) {
					parent::configure($instance);
					$instance->registerRouter($this->container->create(TestRouter::class, ['response']));
				}
			}));
			self::assertEquals(123, $this->application->run());
		}
	}
