<?php
	declare(strict_types=1);

	namespace Edde\Common\Lock;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Lock\ILock;

	class FileLockManager extends AbstractLockManager {
		use LazyContainerTrait;

		/**
		 * @inheritdoc
		 */
		public function createLock(string $name): ILock {
			return $this->lockList[$name] ?? $this->lockList[$name] = $this->container->create(FileLock::class, [
					$name,
				])->setup();
		}
	}
