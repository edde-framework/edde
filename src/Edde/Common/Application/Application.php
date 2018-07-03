<?php
	declare(strict_types = 1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\ApplicationException;
	use Edde\Api\Application\IErrorControl;
	use Edde\Api\Application\LazyRequestTrait;
	use Edde\Api\Application\LazyResponseManagerTrait;
	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Control\IControl;
	use Edde\Api\Converter\LazyConverterManagerTrait;
	use Edde\Api\Log\LazyLogServiceTrait;
	use Edde\Common\Application\Event\ErrorEvent;
	use Edde\Common\Application\Event\FinishEvent;
	use Edde\Common\Application\Event\StartEvent;

	/**
	 * Default application implementation.
	 */
	class Application extends AbstractApplication {
		use LazyContainerTrait;
		use LazyConverterManagerTrait;
		use LazyResponseManagerTrait;
		use LazyRequestTrait;
		use LazyLogServiceTrait;
		/**
		 * @var IErrorControl
		 */
		protected $errorControl;

		/**
		 * @param IErrorControl $errorControl
		 */
		public function lazyErrorControl(IErrorControl $errorControl) {
			$this->errorControl = $errorControl;
		}

		/**
		 * @inheritdoc
		 */
		public function run() {
			try {
				$this->use();
				$this->event(new StartEvent($this));
				list($class, $method, $parameterList) = $this->request->getCurrent();
				if ((($control = $this->container->create($class)) instanceof IControl) === false) {
					throw new ApplicationException(sprintf('Route class [%s] is not instance of [%s].', $class, IControl::class));
				}
				$result = $control->handle($method, $parameterList);
				$this->event(new FinishEvent($this, $result));
				$this->responseManager->execute();
				return $result;
			} catch (\Exception $exception) {
				$this->logService->exception($exception, ['edde']);
				$this->event(new ErrorEvent($this, $exception));
				return $this->errorControl->exception($exception);
			}
		}
	}
