<?php
	declare(strict_types=1);

	namespace Edde\Api\Lock\Inject;

	use Edde\Api\Lock\ILockDirectory;

	trait LockDirectory {
		/**
		 * @var ILockDirectory
		 */
		protected $lockDirectory;

		/**
		 * @param ILockDirectory $lockDirectory
		 */
		public function lazyLockDirectory(ILockDirectory $lockDirectory) {
			$this->lockDirectory = $lockDirectory;
		}
	}
