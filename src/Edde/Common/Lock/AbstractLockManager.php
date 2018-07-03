<?php
	declare(strict_types=1);

	namespace Edde\Common\Lock;

	use Edde\Api\Lock\ILock;
	use Edde\Api\Lock\ILockManager;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	abstract class AbstractLockManager extends Object implements ILockManager {
		use ConfigurableTrait;
		/**
		 * @var ILock[]
		 */
		protected $lockList = [];

		/**
		 * @inheritdoc
		 */
		public function lock(string $name): ILock {
			return $this->createLock($name)->lock();
		}

		/**
		 * @inheritdoc
		 */
		public function block(string $name = null, int $timeout = null): ILock {
			$timeout = $timeout ?: 3;
			$time = microtime(true);
			while (true) {
				if ($timeout > 0 && (microtime(true) - $time) >= $timeout) {
					throw new LockTimeoutException(sprintf('Lock timeout for [%s].', $name));
				}
				try {
					return $this->lock($name);
				} catch (LockedException $exception) {
				}
				usleep(150000);
			}
			return $this->lock($name);
		}

		/**
		 * @inheritdoc
		 */
		public function unlock(string $name): ILock {
			return $this->createLock($name)->unlock();
		}

		/**
		 * @inheritdoc
		 */
		public function kill(string $name): ILock {
			return $this->createLock($name)->kill();
		}

		/**
		 * @inheritdoc
		 */
		public function isLocked(string $name): bool {
			return $this->createLock($name)->isLocked();
		}
	}
