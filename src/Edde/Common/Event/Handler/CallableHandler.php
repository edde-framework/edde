<?php
	declare(strict_types = 1);

	namespace Edde\Common\Event\Handler;

	use Edde\Api\Callback\IParameter;
	use Edde\Api\Event\EventException;
	use Edde\Api\Event\IEvent;
	use Edde\Common\Callback\Callback;
	use Edde\Common\Deffered\DefferedTrait;
	use Edde\Common\Event\AbstractHandler;

	/**
	 * Simple lamda handler.
	 */
	class CallableHandler extends AbstractHandler {
		use DefferedTrait;
		/**
		 * @var callable
		 */
		protected $callable;
		/**
		 * @var string
		 */
		protected $event;

		/**
		 * @param callable $callable
		 * @param string $scope
		 */
		public function __construct(callable $callable, string $scope = null) {
			parent::__construct($scope);
			$this->callable = $callable;
		}

		/** @noinspection PhpInconsistentReturnPointsInspection */
		/**
		 * @inheritdoc
		 */
		public function getIterator() {
			$this->use();
			if (empty($this->event)) {
				return new \ArrayIterator([]);
			}
			yield $this->event => $this->callable;
		}

		/**
		 * @inheritdoc
		 * @throws EventException
		 */
		protected function prepare() {
			$callback = new Callback($this->callable);
			$parameterList = $callback->getParameterList();
			if (count($parameterList) !== 1) {
				throw new EventException('Callable handler lambda can accept only single argument.');
			}
			/** @var $event IParameter */
			$event = reset($parameterList);
			if ($event->hasClass() === false) {
				throw new EventException(sprintf('Callable handler lambda must have type hint implementing [%s].', IEvent::class));
			}
			$class = new \ReflectionClass($this->event = $event->getClass());
			if ($class->implementsInterface(IEvent::class) === false) {
				throw new EventException(sprintf('Callable handler lambda parameter [%s] class [%s] must implement interface [%s].', $event->getName(), $event->getClass(), IEvent::class));
			}
		}
	}
