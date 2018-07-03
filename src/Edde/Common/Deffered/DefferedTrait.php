<?php
	declare(strict_types = 1);

	namespace Edde\Common\Deffered;

	/**
	 * Use this trait where is not possible to use inheritance!
	 */
	trait DefferedTrait {
		/**
		 * @var bool
		 */
		protected $used = false;
		/**
		 * @var callable[]
		 */
		protected $onDefferedList = [];
		/**
		 * @var callable[]
		 */
		protected $onSetupList = [];

		public function onDeffered(callable $callback) {
			if ($this->isUsed()) {
				throw new DefferedException(sprintf('Cannot add %s::onDeffered() callback to already used class [%s].', static::class, static::class));
			}
			$this->onDefferedList[] = $callback;
			return $this;
		}

		public function isUsed() {
			return $this->used;
		}

		public function onSetup(callable $callback) {
			if ($this->isUsed()) {
				throw new DefferedException(sprintf('Cannot add %s::onSetup() callback to already used class [%s].', static::class, static::class));
			}
			$this->onSetupList[] = $callback;
			return $this;
		}

		public function onLoaded(callable $callback) {
			if ($this->isUsed()) {
				$callback($this);
				return $this;
			}
			$this->onSetupList[] = $callback;
			return $this;
		}

		public function use () {
			if ($this->used === false) {
				$this->used = true;
				foreach ($this->onDefferedList as $callback) {
					$callback($this);
				}
				$this->prepare();
				foreach ($this->onSetupList as $callback) {
					$callback($this);
				}
			}
			return $this;
		}

		/**
		 * prepare this class for the first usage
		 */
		protected function prepare() {
		}
	}
