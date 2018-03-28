<?php
	declare(strict_types=1);
	namespace Edde\Service\Storage;

	use Edde\Storage\IStorage;

	trait Storage {
		/** @var IStorage */
		protected $storage;

		/**
		 * @param IStorage $storage
		 */
		public function lazyStorage(IStorage $storage): void {
			$this->storage = $storage;
		}
	}
