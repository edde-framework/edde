<?php
	declare(strict_types=1);

	namespace Edde\Common\Database;

	use Edde\Api\Crate\ICrate;
	use Edde\Api\Database\Exception\DriverException;
	use Edde\Api\Database\Inject\Driver;
	use Edde\Api\Query\IQuery;
	use Edde\Api\Query\IStaticQuery;
	use Edde\Api\Storage\IStorage;
	use Edde\Api\Storage\StorageException;
	use Edde\Api\Storage\TransactionException;
	use Edde\Common\Query\Insert\InsertQuery;
	use Edde\Common\Query\Select\SelectQuery;
	use Edde\Common\Query\Update\UpdateQuery;
	use Edde\Common\Storage\AbstractStorage;
	use PDOException;

	/**
	 * Database (persistant) storage implementation.
	 */
	class DatabaseStorage extends AbstractStorage {
		use Driver;
		/**
		 * @var int
		 */
		protected $transaction = 0;

		/**
		 * @inheritdoc
		 * @throws StorageException
		 */
		public function start(bool $exclusive = false): IStorage {
			if ($this->transaction++ > 0) {
				if ($exclusive === false) {
					return $this;
				}
				throw new TransactionException('Cannot start exclusive transaction, there is already running another one.');
			}
			$this->driver->setup();
			$this->driver->start();
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function commit(): IStorage {
			if (--$this->transaction <= 0) {
				$this->driver->commit();
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function rollback(): IStorage {
			if ($this->transaction === 0) {
				return $this;
			}
			$this->transaction = 0;
			$this->driver->rollback();
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws \Edde\Api\Database\Exception\DriverException
		 * @throws StorageException
		 */
		public function store(ICrate $crate): IStorage {
			$this->driver->setup();
			$schema = $crate->getSchema();
			if ($schema->getMeta('storable', false) === false) {
				throw new StorageException(sprintf('Crate [%s] is not marked as storable (in meta data).', $schema->getSchemaName()));
			}
			$crate->update();
			if ($crate->isDirty() === false) {
				return $this;
			}
			$selectQuery = new SelectQuery();
			$selectQuery->init();
			$identifierList = [];
			foreach ($crate->getIdentifierList() as $property) {
				$schemaProperty = $property->getSchemaProperty();
				$selectQuery->select()->count($schemaProperty->getName(), null)->where()->eq()->property($schemaProperty->getName())->parameter($value = $property->get());
				$identifierList[$schemaProperty->getName()] = $value;
			}
			$selectQuery->from()->source($schema->getSchemaName());
			/** @noinspection ForeachSourceInspection */
			/** @noinspection LoopWhichDoesNotLoopInspection */
			foreach ($this->execute($selectQuery) as $count) {
				break;
			}
			$source = [];
			foreach ($crate->getDirtyList() as $property) {
				$schemaProperty = $property->getSchemaProperty();
				$source[$schemaProperty->getName()] = $property->get();
			}
			$query = ($count = ((int)reset($count) > 0)) ? new UpdateQuery($schema, $source) : new InsertQuery($schema, $source);
			$query->init();
			if ($count) {
				$where = $query->where();
				foreach ($identifierList as $name => $value) {
					$where->eq()->property($name)->parameter($value);
				}
			}
			$this->execute($query);
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws \Edde\Api\Database\Exception\DriverException
		 */
		public function execute(IQuery $query) {
			try {
				$query->setup();
				$this->driver->setup();
				return $this->driver->execute($query);
			} catch (PDOException $e) {
				throw new \Edde\Api\Database\Exception\DriverException(sprintf('Driver [%s] execution failed: %s.', get_class($this->driver), $e->getMessage()), 0, $e);
			}
		}

		/**
		 * @inheritdoc
		 * @throws \Edde\Api\Database\Exception\DriverException
		 */
		public function native(IStaticQuery $staticQuery) {
			try {
				$this->driver->setup();
				return $this->driver->native($staticQuery);
			} catch (PDOException $e) {
				throw new \Edde\Api\Database\Exception\DriverException(sprintf('Driver [%s] execution failed: %s.', get_class($this->driver), $e->getMessage()), 0, $e);
			}
		}

		/**
		 * @inheritdoc
		 */
		protected function handleInit() {
			parent::handleInit();
			$this->transaction = 0;
		}
	}
