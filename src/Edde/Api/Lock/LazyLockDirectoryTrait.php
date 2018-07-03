<?php
	declare(strict_types=1);

	namespace Edde\Api\Lock;

	trait LazyLockDirectoryTrait {
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
