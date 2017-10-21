<?php
	namespace Edde\Api\Storage\Inject;

		use Edde\Api\Storage\IStorage;

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
