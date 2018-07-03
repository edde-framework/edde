<?php
	declare(strict_types=1);

	namespace Edde\Common\Store;

	use Edde\Api\Lock\LazyLockManagerTrait;
	use Edde\Api\Store\IStore;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	abstract class AbstractStore extends Object implements IStore {
		use LazyLockManagerTrait;
		use ConfigurableTrait;

		/**
		 * @inheritdoc
		 */
		public function sete(string $name, $value, int $timeout = null): IStore {
			$this->block($name, $timeout);
			$this->set($name, $value);
			$this->unlock($name);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function iterate() {
			throw new UnsupportedFeatureException(sprintf('Unsupported feature in store [%s].', static::class));
		}

		/**
		 * @inheritdoc
		 */
		public function append(string $name, $value, int $timeout = null): IStore {
			$this->block($name, $timeout);
			$list = is_array($list = $this->get($name, [])) ? $list : [];
			$list[] = $value;
			$this->set($name, $list);
			$this->unlock($name);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function lock(string $name = null, bool $block = true): IStore {
			$this->lockManager->lock($this->getLockName($name));
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function block(string $name = null, int $timeout = null): IStore {
			$this->lockManager->block($this->getLockName($name), $timeout);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function unlock(string $name = null): IStore {
			$this->lockManager->unlock($this->getLockName($name));
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function kill(string $name = null): IStore {
			$this->lockManager->kill($this->getLockName($name));
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function isLocked(string $name = null): bool {
			return $this->lockManager->isLocked($this->getLockName($name));
		}

		/**
		 * @inheritdoc
		 */
		public function pickup(string $name, $default = null, int $timeout = null) {
			$this->block($name, $timeout);
			$item = $this->get($name, $default);
			$this->remove($name);
			$this->unlock($name);
			return $item;
		}

		protected function getLockName(string $name = null): string {
			return static::class . ($name ? '/' . $name : '');
		}
	}
