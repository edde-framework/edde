<?php
	namespace Edde\Common\Storage;

		use Edde\Api\Query\INativeQuery;
		use Edde\Api\Query\IQuery;
		use Edde\Api\Storage\IStorage;
		use Edde\Api\Storage\IStream;
		use Edde\Common\Object\Object;
		use Traversable;

		class Stream extends Object implements IStream {
			/**
			 * @var IStorage
			 */
			protected $storage;
			/**
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
			public function getQuery(): INativeQuery {
			}

			/**
			 * @return array|Traversable|void
			 */
			public function getIterator() {
				yield from $this->storage->execute($this->query);
			}
		}
