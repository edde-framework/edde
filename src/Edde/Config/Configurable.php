<?php
	declare(strict_types=1);
	namespace Edde\Config;

	trait Configurable {
		/** @var IConfigurator[] */
		protected $tConfigurators = [];
		/** @var bool */
		protected $tState = 0;

		/** @inheritdoc */
		public function addConfigurator(IConfigurator $configurator) {
			$this->tConfigurators[] = $configurator;
			return $this;
		}

		/** @inheritdoc */
		public function setConfigurators(array $configurators) {
			$this->tConfigurators = $configurators;
			return $this;
		}

		/** @inheritdoc */
		public function init() {
			if ($this->tState > 0) {
				return $this;
			}
			$this->tState++;
			$this->handleInit();
			return $this;
		}

		/** @inheritdoc */
		public function setup() {
			if ($this->tState > 1) {
				return $this;
			}
			$this->tState++;
			$this->init();
			foreach ($this->tConfigurators as $configHandler) {
				$configHandler->configure($this);
			}
			$this->handleSetup();
			return $this;
		}

		/** @inheritdoc */
		public function isSetup(): bool {
			return $this->tState > 1;
		}

		protected function handleInit(): void {
		}

		protected function handleSetup(): void {
		}
	}
