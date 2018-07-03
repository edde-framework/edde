<?php
	declare(strict_types=1);

	namespace Edde\Api\Lock;

	use Edde\Api\Config\IConfigurable;

	/**
	 * Lock is a mechanism of general locking of an identifier. This feature must be reliable and
	 * also it must be able to create also dead-locks which are not possible with File locks.
	 *
	 * In general when one thread make a lock, another thread should see this id as locked.
	 */
	interface ILockManager extends IConfigurable {
		/**
		 * create a new lock over the given id
		 *
		 * @param string $name
		 *
		 * @return ILock
		 */
		public function lock(string $name): ILock;

		/**
		 * puts lock on the given name; if the lock is already there, function would block for the
		 * given timeout; if zero is provided, timeout is disabled
		 *
		 * @param string|null $name
		 * @param int         $timeout in seconds
		 *
		 * @return ILock
		 */
		public function block(string $name = null, int $timeout = null): ILock;

		/**
		 * unlocks the given id
		 *
		 * @param string $name
		 *
		 * @return ILock
		 */
		public function unlock(string $name): ILock;

		/**
		 * kill a lock of the given id (use wisely!)
		 *
		 * @param string $name
		 *
		 * @return ILock
		 */
		public function kill(string $name): ILock;

		/**
		 * this method is not reliable as the time between "isLocked()" and eventual "lock()"
		 * is not handled and another thread could in meanwhile create a lock
		 *
		 * @param string $name
		 *
		 * @return bool
		 */
		public function isLocked(string $name): bool;

		/**
		 * only creates a lock without any further action
		 *
		 * @param string $name
		 *
		 * @return ILock
		 */
		public function createLock(string $name): ILock;
	}
