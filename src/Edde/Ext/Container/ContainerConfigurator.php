<?php
	declare(strict_types=1);

	namespace Edde\Ext\Container;

	use Edde\Api\Config\IConfigurator;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IFactory;
	use Edde\Common\Config\AbstractConfigurator;

	class ContainerConfigurator extends AbstractConfigurator {
		/**
		 * @var IFactory[]
		 */
		protected $factoryList = [];
		/**
		 * @var IConfigurator[]
		 */
		protected $configuratorList = [];

		public function __construct(array $factoryList, array $configuratorList) {
			$this->factoryList = $factoryList;
			$this->configuratorList = $configuratorList;
		}

		/**
		 * @param IContainer $instance
		 */
		public function configure($instance) {
			$instance->registerFactoryList($this->factoryList);
			$configuratorList = [];
			foreach ($this->configuratorList as $name => $configHandler) {
				foreach (is_array($configHandler) ? $configHandler : [$configHandler] as $config) {
					$configuratorList[$name] = is_string($config) ? $instance->create($config, [], __METHOD__) : $config;
				}
			}
			$instance->registerConfiguratorList($configuratorList);
		}
	}
