<?php
	declare(strict_types = 1);

	namespace Edde\Common\Storage;

	use Edde\Api\Query\IQuery;
	use Edde\Api\Storage\IBoundQuery;
	use Edde\Api\Storage\IStorage;
	use Edde\Common\AbstractObject;

	class BoundQuery extends AbstractObject implements IBoundQuery {
		/**
		 * @var IQuery
		 */
		protected $query;
		/**
		 * @var IStorage
		 */
		protected $storage;

		/**
		 * @inheritdoc
		 */
		public function bind(IQuery $query, IStorage $storage): IBoundQuery {
			$this->query = $query;
			$this->storage = $storage;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function execute() {
			return $this->storage->execute($this->query);
		}
	}
