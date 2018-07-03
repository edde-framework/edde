<?php
	declare(strict_types=1);

	namespace Edde\Api\Storage;

	/**
	 * Implements dependency for a storage interface.
	 */
	trait LazyStorageTrait {
		/**
		 * @var IStorage
		 */
		protected $storage;

		/**
		 * @param IStorage $storage
		 */
		public function lazyStorage(IStorage $storage) {
			$this->storage = $storage;
		}
	}
