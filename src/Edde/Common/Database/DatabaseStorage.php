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
				if ($entity->isDirty() === false) {
					return $this;
				}
				$query = new SelectQuery();
				$query->setDescription('check entity existence query');
				$query->table($entity->getSchema()->getName())->all();
				$where = $query->where();
				foreach (($primaryList = $entity->getPrimaryList()) as $property) {
					$where->and()->eq($name = $property->getName())->to($property->get());
				}
				/**
				 * pickup an entity from storage if it's already there (and run update)
				 */
				foreach ($this->execute($query) as $_) {
					return $this->update($entity);
				}
				return $this->insert($entity);
			}

			/**
			 * @inheritdoc
			 */
			public function insert(IEntity $entity): IStorage {
				if ($entity->isDirty() === false) {
					return $this;
				}
				$this->execute(new InsertQuery($entity->getSchema()->getName(), $this->prepare($entity)));
				$entity->commit();
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function update(IEntity $entity): IStorage {
				if ($entity->isDirty() === false) {
					return $this;
				}
				$query = new UpdateQuery($entity->getSchema()->getName(), $this->prepare($entity));
				$where = $query->where();
				foreach ($entity->getPrimaryList() as $property) {
					$where->and()->eq($property->getName())->to($property->get());
				}
				$this->execute($query);
				$entity->commit();
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function collection(string $schema, IQuery $query): ICollection {
				return new Collection($this->entityManager, $this, $query, $schema);
			}

			protected function prepare(IEntity $entity): array {
				$schema = $entity->getSchema();
				$schemaName = $schema->getName();
				$source = [];
				foreach ($entity->getPrimaryList() as $property) {
					$source[$property->getName()] = $property->get();
				}
				foreach ($entity->getDirtyProperties() as $property) {
					$source[$property->getName()] = $property->get();
				}
				$entity->push($source = $this->schemaManager->generate($schemaName, $source));
				return $this->schemaManager->sanitize($schemaName, $source);
			}
		}
