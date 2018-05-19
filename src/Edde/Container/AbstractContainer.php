<?php
	declare(strict_types=1);
	namespace Edde\Container;

	use Edde\Config\IConfigurator;
	use Edde\Edde;

	abstract class AbstractContainer extends Edde implements IContainer {
		/** @var IFactory[] */
		protected $factories;
		/** @var IConfigurator[][] */
		protected $configurators;

		/**
		 * @param IFactory[]      $factories
		 * @param IConfigurator[] $configurators
		 */
		public function __construct(array $factories = [], array $configurators = []) {
			$this->factories = $factories;
			$this->configurators = $configurators;
		}

		/** @inheritdoc */
		public function registerFactory(IFactory $factory): IContainer {
			if ($uuid = $factory->getUuid()) {
				$this->factories[$uuid] = $this->factories['dependency:' . $uuid] = $factory;
				return $this;
			}
			$this->factories[] = $factory;
			return $this;
		}

		/** @inheritdoc */
		public function registerFactories(array $factories): IContainer {
			foreach ($factories as $factory) {
				$this->registerFactory($factory);
			}
			return $this;
		}

		/** @inheritdoc */
		public function registerConfigurator(string $name, IConfigurator $configurator): IContainer {
			$this->configurators[$name][] = $configurator;
			return $this;
		}

		/** @inheritdoc */
		public function registerConfigurators(array $configurators): IContainer {
			foreach ($configurators as $name => $configurator) {
				$this->registerConfigurator($name, $configurator);
			}
			return $this;
		}

		/** @inheritdoc */
		public function canHandle(string $dependency): bool {
			try {
				$this->getFactory($dependency);
				return true;
			} catch (ContainerException $exception) {
				return false;
			}
		}

		/** @inheritdoc */
		public function create(string $name, array $params = [], string $source = null) {
			return $this->factory($this->getFactory($name, $source), $name, $params, $source);
		}
	}
