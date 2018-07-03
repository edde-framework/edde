<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container\Factory;

	use Edde\Api\Container\FactoryException;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IFactory;
	use Edde\Common\AbstractObject;
	use Edde\Common\Container\FactoryLockException;

	/**
	 * Basic implementation for all dependency factories.
	 */
	abstract class AbstractFactory extends AbstractObject implements IFactory {
		/**
		 * @var string
		 */
		protected $name;
		/**
		 * @var bool
		 */
		protected $singleton;
		/**
		 * @var callable[]
		 */
		protected $onSetupList = [];
		protected $instance;
		/**
		 * @var bool[]
		 */
		protected $lockList = [];

		/**
		 * Obsolete: Any computer you own.
		 *
		 * @param string $name
		 * @param bool $singleton
		 */
		public function __construct(string $name, bool $singleton = true) {
			$this->name = $name;
			$this->singleton = $singleton;
		}

		/**
		 * @inheritdoc
		 */
		public function getName(string $name = null): string {
			return $name ?: $this->name;
		}

		/**
		 * @inheritdoc
		 */
		public function deffered(callable $callback): IFactory {
			$this->onSetupList[] = $callback;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function canHandle(string $name): bool {
			return $this->name === $name;
		}

		/**
		 * @inheritdoc
		 * @throws FactoryException
		 */
		public function create(string $name, array $parameterList, IContainer $container) {
			if ($this->instance !== null) {
				if ($this->isSingleton()) {
					return $this->instance;
				}
			}
			if (isset($this->lockList[$name]) !== false && $this->lockList[$name] === true) {
				throw new FactoryLockException(sprintf("Factory [%s] is locked; isn't there some circular dependency?", $this->name));
			}
			try {
				$this->lockList[$name] = true;
				$container->inject($this->instance = $this->factory($name, $parameterList, $container));
				$this->setup($this->instance, $container);
				return $this->instance;
			} finally {
				unset($this->lockList[$name]);
			}
		}

		/**
		 * @inheritdoc
		 */
		public function isSingleton(): bool {
			return $this->singleton;
		}

		/**
		 * @inheritdoc
		 */
		public function setSingleton(bool $singleton): IFactory {
			$this->singleton = $singleton;
			return $this;
		}

		/**
		 * execute setup callbacks on registered callbacks
		 *
		 * @param mixed $instance
		 * @param IContainer $container
		 *
		 * @return mixed
		 */
		protected function setup($instance, IContainer $container) {
			foreach ($this->onSetupList as $callback) {
				$container->call($callback, $instance);
			}
			return $instance;
		}

		/**
		 * execute cache method with all required parameters already provided
		 *
		 * @param string $name
		 * @param array $parameterList
		 * @param IContainer $container
		 *
		 * @return mixed
		 */
		abstract public function factory(string $name, array $parameterList, IContainer $container);
	}
