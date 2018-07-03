<?php
	declare(strict_types=1);

	namespace Edde\Common\Container;

	use Edde\Api\Config\IConfigurator;
	use Edde\Api\Container\ContainerException;
	use Edde\Api\Container\FactoryException;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IFactory;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Container\Factory\CallbackFactory;
	use Edde\Common\Object;

	abstract class AbstractContainer extends Object implements IContainer {
		use ConfigurableTrait;
		/**
		 * @var IFactory[]
		 */
		protected $factoryList = [];
		/**
		 * @var IConfigurator[][]
		 */
		protected $configuratorList = [];

		/**
		 * @inheritdoc
		 */
		public function registerFactory(IFactory $factory, string $id = null): IContainer {
			if ($id !== null) {
				$this->factoryList[$id] = $factory;
				return $this;
			}
			$this->factoryList[] = $factory;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function registerFactoryList(array $factoryList): IContainer {
			$this->factoryList = [];
			foreach ($factoryList as $id => $factory) {
				$this->registerFactory($factory, is_string($id) ? $id : null);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function registerConfigurator(string $name, IConfigurator $configurator): IContainer {
			$this->configuratorList[$name][] = $configurator;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function registerConfiguratorList(array $configuratorList): IContainer {
			$this->configuratorList = [];
			foreach ($configuratorList as $name => $configurator) {
				$this->registerConfigurator($name, $configurator);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws FactoryException
		 * @throws ContainerException
		 */
		public function create(string $name, array $parameterList = [], string $source = null) {
			return $this->factory($this->getFactory($name, $source), $parameterList, $name, $source);
		}

		/**
		 * @inheritdoc
		 * @throws FactoryException
		 * @throws ContainerException
		 */
		public function call(callable $callable, array $parameterList = [], string $source = null) {
			return $this->factory(new CallbackFactory($callable), $parameterList, $source);
		}
	}
