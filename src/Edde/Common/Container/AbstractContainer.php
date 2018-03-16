<?php
	declare(strict_types=1);
	namespace Edde\Common\Container;

	use Edde\Api\Config\IConfigurator;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IFactory;
	use Edde\Exception\Container\UnknownFactoryException;
	use Edde\Object;

	abstract class AbstractContainer extends Object implements IContainer {
		/** @var IFactory[] */
		protected $factories = [];
		/** @var IConfigurator[][] */
		protected $configurators = [];

		/**
		 * @inheritdoc
		 */
		public function registerFactory(IFactory $factory, string $id = null): IContainer {
			if ($id !== null) {
				$this->factories[$id] = $factory;
				return $this;
			}
			$this->factories[] = $factory;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function registerFactories(array $factories): IContainer {
			$this->factories = [];
			foreach ($factories as $id => $factory) {
				$this->registerFactory($factory, is_string($id) ? $id : null);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function registerConfigurator(string $name, IConfigurator $configurator): IContainer {
			$this->configurators[$name][] = $configurator;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function registerConfigurators(array $configurators): IContainer {
			$this->configurators = [];
			foreach ($configurators as $name => $configurator) {
				$this->registerConfigurator($name, $configurator);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function canHandle(string $dependency): bool {
			try {
				$this->getFactory($dependency);
				return true;
			} catch (UnknownFactoryException $exception) {
				return false;
			}
		}

		/**
		 * @inheritdoc
		 */
		public function create(string $name, array $parameterList = [], string $source = null) {
			return $this->factory($this->getFactory($name, $source), $name, $parameterList, $source);
		}
	}
