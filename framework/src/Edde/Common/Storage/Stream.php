<?php
	declare(strict_types=1);
	namespace Edde\Common\Storage;

	use Edde\Api\Storage\IStorage;
	use Edde\Api\Storage\IStream;
	use Edde\Api\Storage\Query\IQuery;
	use Edde\Api\Storage\Query\ISelectQuery;
	use Edde\Common\Object\Object;

	class Stream extends Object implements IStream {
		/** @var IStorage */
		protected $storage;
		/** @var ISelectQuery */
		protected $query;

		public function __construct(IStorage $storage, IQuery $query) {
			$this->storage = $storage;
			$this->query = $query;
		}

		/** @inheritdoc */
		public function query(ISelectQuery $query): IStream {
			$this->query = $query;
			return $this;
		}

		/** @inheritdoc */
		public function getQuery(): ISelectQuery {
			return $this->query;
		}

		/** @inheritdoc */
		public function getIterator() {
			yield from $this->storage->execute($this->query);
		}

		/** @inheritdoc */
		public function __clone() {
			parent::__clone();
			$this->query = clone $this->query;
		}
	}
