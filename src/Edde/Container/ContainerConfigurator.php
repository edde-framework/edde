<?php
	declare(strict_types=1);
	namespace Edde\Container;

	use Edde\Configurable\AbstractConfigurator;
	use Edde\Configurable\IConfigurator;

	class ContainerConfigurator extends AbstractConfigurator {
		/** @var IFactory[] */
		protected $factories = [];
		/** @var IConfigurator[] */
		protected $configurators = [];

		public function __construct(array $factories, array $configurators) {
			$this->factories = $factories;
			$this->configurators = $configurators;
		}

		/**
		 * @param IContainer $instance
		 *
		 * @throws ContainerException
		 */
		public function configure($instance) {
			parent::configure($instance);
			$instance->registerFactories($this->factories);
			$configurators = [];
			foreach ($this->configurators as $name => $configHandler) {
				foreach (is_array($configHandler) ? $configHandler : [$configHandler] as $config) {
					$configurators[$name] = is_string($config) ? $instance->create($config, [], __METHOD__) : $config;
				}
			}
			$instance->registerConfigurators($configurators);
		}
	}
