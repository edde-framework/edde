<?php
	declare(strict_types=1);
	namespace Edde\Configurable;

	trait Configurable {
		/** @var IConfigurator[] */
		protected $tConfigurators = [];
		/** @var bool */
		protected $tState = 0;

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
			$this->handleInit();
			$this->tState++;
			return $this;
		}

		/** @inheritdoc */
		public function setup() {
			if ($this->tState > 1) {
				return $this;
			}
			$this->init();
			foreach ($this->tConfigurators as $configHandler) {
				$configHandler->configure($this);
			}
			$this->handleSetup();
			$this->tState++;
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
