<?php
	namespace Edde\Common\Storage;

		use Edde\Api\Query\IQuery;
		use Edde\Api\Storage\ICollection;
		use Edde\Api\Storage\IStorage;
		use Edde\Common\Object\Object;

		class Collection extends Object implements ICollection {
			/**
			 * source for this collection
			 *
			 * @var IStorage
			 */
			protected $storage;
			/**
			 * query being executed on the storage
			 *
			 * @var IQuery
			 */
			protected $query;

			public function __construct(IStorage $storage, IQuery $query) {
				$this->storage = $storage;
				$this->query = $query;
			}

			/**
			 * @inheritdoc
			 */
			public function getIterator() {
				foreach ($this->storage->execute($this->query) as $source) {
					yield 'foo';
				}
			}
		}
