<?php
	namespace Edde\Common\Database;

		use Edde\Api\Database\Inject\Driver;
		use Edde\Api\Query\INativeQuery;
		use Edde\Api\Query\IQuery;
		use Edde\Api\Schema\Inject\SchemaManager;
		use Edde\Api\Storage\Exception\ExclusiveTransactionException;
		use Edde\Api\Storage\Exception\NoTransactionException;
		use Edde\Api\Storage\ICollection;
		use Edde\Api\Storage\IEntity;
		use Edde\Api\Storage\Inject\EntityManager;
		use Edde\Api\Storage\IStorage;
		use Edde\Common\Query\InsertQuery;
		use Edde\Common\Query\NativeQuery;
		use Edde\Common\Query\SelectQuery;
		use Edde\Common\Query\UpdateQuery;
		use Edde\Common\Storage\AbstractStorage;
		use Edde\Common\Storage\Collection;

		class DatabaseStorage extends AbstractStorage {
			use EntityManager;
			use SchemaManager;
			use Driver;
			/**
			 * @var int
			 */
			protected $transaction = 0;

			/**
			 * @inheritdoc
			 */
			public function execute(IQuery $query) {
				return $this->driver->execute($query);
			}

			/**
			 * @inheritdoc
			 */
			public function query($query, array $parameterList = []) {
				return $this->driver->native(new NativeQuery($query, $parameterList));
			}

			/**
			 * @inheritdoc
			 */
			public function native(INativeQuery $nativeQuery) {
				return $this->driver->native($nativeQuery);
			}

			/**
			 * @inheritdoc
			 */
			public function start(bool $exclusive = false): IStorage {
				if ($this->transaction++ > 0) {
					if ($exclusive === false) {
						return $this;
					}
					throw new ExclusiveTransactionException('Cannot start an exclusive transaction; there is already another one running.');
				}
				$this->driver->start();
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function commit(): IStorage {
				if ($this->transaction === 0) {
					throw new NoTransactionException('Cannot commit a transaction - there is no one running!');
				} else if (--$this->transaction === 0) {
					$this->driver->commit();
				}
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function rollback(): IStorage {
				if ($this->transaction === 0) {
					throw new NoTransactionException('Cannot rollback a transaction - there is no one running!');
				} else if (--$this->transaction === 0) {
					$this->driver->rollback();
				}
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function save(IEntity $entity): IStorage {
				/**
				 * entities not changed will not be saved
				 */
				if ($this->entityManager->isDirty($entity) === false) {
					return $this;
				}
				$schema = $entity->getSchema();
				$query = new SelectQuery();
				$query->setDescription('check entity existence query');
				$query->table($schemaName = $schema->getName())->all();
				$where = $query->where();
				foreach (($primaryList = $entity->getPrimaryList()) as $property) {
					$where->and()->eq($name = $property->getName())->to($property->get());
				}
				foreach ($this->execute($query) as $hasEntity) {
					break;
				}
				$source = [];
				foreach ($this->entityManager->getDirtyProperties($entity) as $property) {
					$source[$property->getName()] = $property->get();
				}
				$source = $this->schemaManager->sanitize($schemaName, $this->schemaManager->generate($schemaName, $source));
				$query = isset($hasEntity) ? new UpdateQuery($schemaName, $source) : new InsertQuery($schemaName, $source);
				if (isset($hasEntity)) {
					$where = $query->where();
					foreach ($primaryList as $property) {
						$where->and()->eq($property->getName())->to($property->get());
					}
				}
				$this->execute($query);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function collection(string $schema, IQuery $query): ICollection {
				return new Collection($schema, $this->entityManager, $this, $query);
			}
		}
