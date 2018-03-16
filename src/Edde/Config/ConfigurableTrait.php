<?php
	declare(strict_types=1);
	namespace Edde\Config;

	use Edde\Api\Config\IConfigurator;

	trait ConfigurableTrait {
		/**
		 * @var IConfigurator[]
		 */
		protected $tConfigurators = [];
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
			$this->tConfigurators[] = $configurator;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function setConfigurators(array $configurators) {
			$this->tConfigurators = $configurators;
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
			foreach ($this->tConfigurators as $configHandler) {
				$configHandler->configure($this);
			}
			$this->handleSetup();
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function isSetup(): bool {
			return $this->tSetup;
		}

		protected function handleInit(): void {
		}

		protected function handleSetup(): void {
		}
	}
