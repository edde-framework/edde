<?php
	declare(strict_types=1);

	namespace Edde\Common\Storage;

	use Edde\Api\Crate\ICrateFactory;
	use Edde\Api\Query\IQuery;
	use Edde\Api\Storage\ICollection;
	use Edde\Api\Storage\IStorage;
	use Edde\Common\Object;

	/**
	 * Default implementation of collection.
	 */
	class Collection extends Object implements ICollection {
		/**
		 * @var string
		 */
		protected $schema;
		/**
		 * @var IStorage
		 */
		protected $storage;
		/**
		 * @var ICrateFactory
		 */
		protected $crateFactory;
		/**
		 * @var IQuery
		 */
		protected $query;
		/**
		 * @var string
		 */
		protected $crate;

		/**
		 * @param string        $schema
		 * @param IStorage      $storage
		 * @param ICrateFactory $crateFactory
		 * @param IQuery        $query
		 * @param string        $crate
		 */
		public function __construct(string $schema, IStorage $storage, ICrateFactory $crateFactory, IQuery $query, string $crate = null) {
			$this->schema = $schema;
			$this->storage = $storage;
			$this->crateFactory = $crateFactory;
			$this->query = $query;
			$this->crate = $crate;
		}

		/**
		 * @inheritdoc
		 */
		public function getQuery() {
			return $this->query;
		}

		/**
		 * @inheritdoc
		 */
		public function getIterator() {
			/** @noinspection ForeachSourceInspection */
			foreach ($this->storage->execute($this->query) as $item) {
				$crate = $this->crateFactory->crate($this->schema, (array)$item, $this->crate);
				$schema = $crate->getSchema();
				foreach ($schema->getLinkList() as $schemaLink) {
					$crate->proxy($schemaLink->getName(), [
						$this->storage,
						'getLink',
					]);
				}
				yield $crate;
			}
		}
	}
