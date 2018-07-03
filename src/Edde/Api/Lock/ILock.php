<?php
	declare(strict_types=1);

	namespace Edde\Api\Lock;

	use Edde\Api\Config\IConfigurable;

	/**
	 * Lock descriptor; could be used to actually control a lock.
	 */
	interface ILock extends IConfigurable {
		/**
		 * get lock name/id
		 *
		 * @return string
		 */
		public function getName(): string;

		/**
		 * creates (executes) the lock
		 *
		 * @return ILock
		 */
		public function lock(): ILock;

		/**
		 * tells if the given lock already exists (or tells internal state of a lock)
		 *
		 * @return bool
		 */
		public function isLocked(): bool;

		/**
		 * releases the lock
		 *
		 * @return ILock
		 */
		public function unlock(): ILock;

		/**
		 * kill the lock without any questions
		 *
		 * @return ILock
		 */
		public function kill(): ILock;
	}
