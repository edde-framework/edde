<?php
	declare(strict_types=1);

	namespace Edde\Api\Storage\Inject;

	use Edde\Api\Storage\IStorage;

	/**
	 * Implements dependency for a storage interface.
	 */
	trait Storage {
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
