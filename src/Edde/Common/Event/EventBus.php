<?php
	declare(strict_types = 1);

	namespace Edde\Common\Event;

	use Edde\Api\Container\ILazyInject;
	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Event\EventException;
	use Edde\Api\Event\IEvent;
	use Edde\Api\Event\IEventBus;
	use Edde\Api\Event\IHandler;
	use Edde\Common\AbstractObject;

	/**
	 * Default simple implementation of an EventBus.
	 */
	class EventBus extends AbstractObject implements IEventBus, ILazyInject {
		use LazyContainerTrait;
		/**
		 * @var bool
		 */
		protected $used = false;
		/**
		 * @var callable[][][]
		 */
		protected $listenList = [];
		/**
		 * @var IHandler[]
		 */
		protected $handlerList = [];
		/**
		 * @var IEventBus
		 */
		protected $chain;
		/**
		 * @var \SplStack
		 */
		protected $scopeStack;

		/**
		 * @inheritdoc
		 * @throws EventException
		 */
		public function handler(IHandler $handler): IEventBus {
			if ($this->used) {
				$this->listen($handler);
				return $this;
			}
			$this->handlerList[] = $handler;
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws EventException
		 */
		public function listen($listen, string $scope = null): IEventBus {
			if (($listen instanceof IHandler) === false) {
				$listen = HandlerFactory::handler($listen, $scope);
			}
			foreach ($listen as $event => $callable) {
				$this->register($event, $callable, $listen->getScope());
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function register(string $event, callable $handler, string $scope = null): IEventBus {
			$this->listenList[$scope][$event][] = $handler;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function chain(IEventBus $eventBus): IEventBus {
			$this->chain = $eventBus;
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws \Edde\Api\Event\EventException
		 */
		public function scope(callable $callback, ...$handlerList) {
			$this->prepare();
			$this->scopeStack->push($scopeId = hash('sha256', random_bytes(256)));
			foreach ($handlerList as $handler) {
				$this->listen($handler, $scopeId);
			}
			try {
				return $callback();
			} catch (\Exception $e) {
				/** @noinspection ExceptionsAnnotatingAndHandlingInspection */
				throw $e;
			} finally {
				unset($this->listenList[$scopeId]);
				$this->scopeStack->pop();
			}
		}

		protected function prepare() {
			if ($this->used) {
				return;
			}
			$this->used = true;
			$this->scopeStack = new \SplStack();
			foreach ($this->handlerList as $handler) {
				$this->listen($handler);
			}
		}

		/**
		 * @inheritdoc
		 */
		public function event(IEvent $event, string $scope = null): IEventBus {
			$this->prepare();
			/** @noinspection CallableParameterUseCaseInTypeContextInspection */
			$scope = $scope ?: ($this->scopeStack->isEmpty() ? null : $this->scopeStack->top());
			if (isset($this->listenList[$scope][$name = get_class($event)]) === false) {
				if ($this->chain) {
					$this->chain->event($event, $scope);
				}
				return $this;
			}
			if ($this->container) {
				foreach ($this->listenList[$scope][$name] as $callback) {
					$this->container->call($callback, $event);
				}
				return $this;
			}
			foreach ($this->listenList[$scope][$name] as $callback) {
				$callback($event);
			}
			if ($this->chain) {
				$this->chain->event($event, $scope);
			}
			return $this;
		}
	}
