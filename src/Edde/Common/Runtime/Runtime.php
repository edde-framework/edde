<?php
	declare(strict_types = 1);

	namespace Edde\Common\Runtime;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Runtime\IRuntime;
	use Edde\Api\Runtime\ISetupHandler;
	use Edde\Common\Deffered\AbstractDeffered;
	use Edde\Common\Runtime\Event\BootstrapEvent;
	use Edde\Common\Runtime\Event\ExceptionEvent;
	use Edde\Common\Runtime\Event\ShutdownEvent;

	/**
	 * Low level class responsible for basic system preparation. If application is not used, this should be present
	 * all the times.
	 */
	class Runtime extends AbstractDeffered implements IRuntime {
		/**
		 * @var ISetupHandler
		 */
		protected $setupHandler;
		/**
		 * @var IContainer
		 */
		protected $container;

		/**
		 * @param ISetupHandler $setupHandler
		 */
		public function __construct(ISetupHandler $setupHandler) {
			$this->setupHandler = $setupHandler;
		}

		/**
		 * execute the given callback with the given ISetupHandler; automagically register current IRuntime and ISetupHandler into IContainer
		 *
		 * @param ISetupHandler $setupHandler
		 * @param callable $callback
		 *
		 * @return mixed
		 * @throws \Exception
		 */
		static public function execute(ISetupHandler $setupHandler, callable $callback) {
			$runtime = new self($setupHandler);
			$setupHandler->registerFactoryList([
				IRuntime::class => $runtime,
				ISetupHandler::class => $setupHandler,
			]);
			return $runtime->run($callback);
		}

		/**
		 * @inheritdoc
		 * @throws \Exception
		 */
		public function run(callable $callback) {
			$this->use();
			$this->setupHandler->event(new BootstrapEvent($this->container));
			try {
				$result = $this->container->call($callback);
				$this->setupHandler->event(new ShutdownEvent($this->container));
				return $result;
			} catch (\Exception $exception) {
				$this->setupHandler->event($exceptionEvent = new ExceptionEvent($exception));
				if ($exceptionEvent->hasResult()) {
					return $exceptionEvent->getResult();
				}
				throw $exception;
			}
		}

		/**
		 * @inheritdoc
		 */
		public function isConsoleMode() {
			return php_sapi_name() === 'cli';
		}

		/**
		 * @inheritdoc
		 */
		protected function prepare() {
			$this->container = $this->setupHandler->createContainer();
		}
	}
