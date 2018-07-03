<?php
	declare(strict_types=1);

	namespace Edde\Common\Lock;

	use Edde\Api\Lock\ILock;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Lock\Exception\ForeignLockException;
	use Edde\Common\Lock\Exception\LockedException;
	use Edde\Common\Lock\Exception\UnlockedException;
	use Edde\Common\Object\Object;

	abstract class AbstractLock extends Object implements ILock {
		use ConfigurableTrait;
		/**
		 * @var string
		 */
		protected $name;
		/**
		 * @var bool
		 */
		protected $current = false;
		/**
		 * @var bool
		 */
		protected $lock = false;

		public function __construct(string $name) {
			$this->name = $name;
		}

		/**
		 * @inheritdoc
		 */
		public function getName(): string {
			return $this->name;
		}

		/**
		 * @inheritdoc
		 */
		public function lock(): ILock {
			if ($this->locked()) {
				throw new LockedException(sprintf('The name (id) [%s] is already locked.', $this->getName()));
			}
			$this->current = $this->lock = true;
			return $this->onLock();
		}

		/**
		 * @inheritdoc
		 */
		public function isLocked(): bool {
			if ($this->current) {
				return $this->lock;
			}
			return $this->locked();
		}

		/**
		 * @inheritdoc
		 */
		public function unlock(): ILock {
			if ($this->current === false) {
				throw new ForeignLockException(sprintf('Lock [%s] cannot be unlocked because it was created by another lock (or in another thread). Use %s::kill() to kill the lock.', $this->getName(), ILock::class));
			} else if ($this->lock === false) {
				throw new UnlockedException(sprintf('Current lock [%s] has not been locked. Cannot call [%s] on an already released (or non existent) lock.', $this->getName(), __METHOD__));
			}
			$this->lock = false;
			return $this->onUnlock();
		}

		/**
		 * @inheritdoc
		 */
		public function kill(): ILock {
			return $this->onUnlock();
		}

		abstract protected function onLock(): ILock;

		abstract protected function onUnlock(): ILock;

		abstract protected function locked(): bool;
	}
