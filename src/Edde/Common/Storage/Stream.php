<?php
	namespace Edde\Common\Storage;

		use Edde\Api\Query\ISelectQuery;
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
			 * @var ISelectQuery
			 */
			protected $query;

			public function __construct(IStorage $storage, ISelectQuery $query) {
				$this->storage = $storage;
				$this->query = $query;
			}

			/**
			 * @inheritdoc
			 */
			public function getQuery(): ISelectQuery {
				return $this->query;
			}

			/**
			 * @return array|Traversable|void
			 */
			public function getIterator() {
				yield from $this->storage->execute($this->query);
			}

			public function __clone() {
				parent::__clone();
				$this->query = clone $this->query;
			}
		}
