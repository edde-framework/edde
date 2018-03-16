<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Exception\Storage\InvalidSourceException;
	use Edde\Object;
	use Edde\Query\IQuery;
	use Edde\Storage\Query\ISelectQuery;
	use function explode;
	use function strpos;

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
		public function emit(array $source): array {
			$item = [];
			foreach ($source as $k => $v) {
				if (strpos($k, '.') === false) {
					throw new InvalidSourceException(sprintf('Stream get an item without dot notation; cannot resolve alias to schema.'));
				}
				[$alias, $property] = explode('.', $k, 2);
				$item[$alias][$property] = $v;
			}
			return $item;
		}

		/** @inheritdoc */
		public function getIterator() {
			foreach ($this->storage->execute($this->query) as $array) {
				yield $this->emit($array);
			}
		}

		/** @inheritdoc */
		public function __clone() {
			parent::__clone();
			$this->query = clone $this->query;
		}
	}
