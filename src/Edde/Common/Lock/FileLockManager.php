<?php
	declare(strict_types=1);

	namespace Edde\Common\Lock;

	use Edde\Api\Container\Inject\Container;
	use Edde\Api\Lock\ILock;

	class FileLockManager extends AbstractLockManager {
		use Container;

		/**
		 * @inheritdoc
		 */
		public function createLock(string $name): ILock {
			return $this->lockList[$name] ?? $this->lockList[$name] = $this->container->create(FileLock::class, [
					$name,
				])->setup();
		}
	}
