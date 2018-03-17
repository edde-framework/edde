<?php
	declare(strict_types=1);
	namespace Edde\Inject\Storage;

	use Edde\Storage\IStorage;

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
