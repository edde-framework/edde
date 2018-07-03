<?php
	declare(strict_types=1);

	namespace Edde\Common\Config;

	use Edde\Api\Config\IConfigurator;

	trait ConfigurableTrait {
		/**
		 * @var IConfigurator[]
		 */
		protected $tConfiguratorList = [];
		/**
		 * @var bool
		 */
		protected $tInit = false;
		/**
		 * @var bool
		 */
		protected $tSetup = false;

		/**
		 * @inheritdoc
		 */
		public function addConfigurator(IConfigurator $configurator) {
			$this->tConfiguratorList[] = $configurator;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function setConfiguratorList(array $configuratorList) {
			$this->tConfiguratorList = $configuratorList;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function init() {
			if ($this->tInit) {
				return $this;
			}
			$this->tInit = true;
			$this->handleInit();
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function setup() {
			if ($this->tSetup) {
				return $this;
			}
			$this->tSetup = true;
			$this->init();
			foreach ($this->tConfiguratorList as $configHandler) {
				$configHandler->configure($this);
			}
			$this->handleSetup();
			return $this;
		}

		protected function handleInit() {
		}

		protected function handleSetup() {
		}
	}
