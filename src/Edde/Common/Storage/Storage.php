<?php
	namespace Edde\Common\Storage;

		use Edde\Api\Driver\Inject\Driver;
		use Edde\Api\Query\INativeBatch;
		use Edde\Api\Query\INativeQuery;
		use Edde\Api\Query\Inject\QueryBuilder;
		use Edde\Api\Query\IQuery;
		use Edde\Api\Schema\Inject\SchemaManager;
		use Edde\Api\Storage\Exception\ExclusiveTransactionException;
		use Edde\Api\Storage\Exception\NoTransactionException;
		use Edde\Api\Storage\ICollection;
		use Edde\Api\Storage\IEntity;
		use Edde\Api\Storage\Inject\EntityManager;
		use Edde\Api\Storage\IStorage;
		use Edde\Common\Object\Object;
		use Edde\Common\Query\CreateSchemaQuery;
		use Edde\Common\Query\InsertQuery;
		use Edde\Common\Query\NativeQuery;
		use Edde\Common\Query\SelectQuery;
		use Edde\Common\Query\UpdateQuery;

		class Storage extends Object implements IStorage {
			use EntityManager;
			use SchemaManager;
			use QueryBuilder;
			use Driver;
			/**
			 * @var int
			 */
			protected $transaction = 0;

			/**
			 * @inheritdoc
			 */
			public function execute(IQuery $query) {
				return $this->batch($this->queryBuilder->build($query));
			}

			/**
			 * @inheritdoc
			 */
			public function query($query, array $parameterList = []) {
				return $this->native(new NativeQuery($query, $parameterList));
			}

			/**
			 * @inheritdoc
			 */
			public function native(INativeQuery $nativeQuery) {
				return $this->driver->execute($nativeQuery);
			}

			/**
			 * @inheritdoc
			 */
			public function batch(INativeBatch $nativeBatch) {
				try {
					$this->start();
					$result = $this->driver->batch($nativeBatch);
					$this->commit();
					return $result;
				} catch (\Throwable $throwable) {
					$this->rollback();
					throw $throwable;
				}
			}

			/**
			 * @inheritdoc
			 */
			public function start(bool $exclusive = false): IStorage {
				if ($this->transaction > 0) {
					if ($exclusive === false) {
						$this->transaction++;
						return $this;
					}
					throw new ExclusiveTransactionException('Cannot start an exclusive transaction; there is already another one running.');
				}
				$this->driver->start();
				$this->transaction++;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function commit(): IStorage {
				if ($this->transaction === 0) {
					throw new NoTransactionException('Cannot commit a transaction - there is no one running!');
				} else if ($this->transaction === 1) {
					$this->driver->commit();
				}
				/**
				 * it's intentional to lower the number of transaction after commit as a driver could throw an
				 * exception, thus transaction state could not be consistent
				 */
				$this->transaction--;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function rollback(): IStorage {
				if ($this->transaction === 0) {
					throw new NoTransactionException('Cannot rollback a transaction - there is no one running!');
				} else if ($this->transaction === 1) {
					$this->driver->rollback();
				}
				$this->transaction--;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function saveRelations(IEntity $entity): IStorage {
				foreach ($entity->getLinkList() as $entity) {
					$this->save($entity);
				}
				foreach ($entity->getRelationList() as $entity) {
					$this->save($entity);
				}
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function save(IEntity $entity): IStorage {
				$this->start();
				try {
					$this->saveRelations($entity);
					/**
					 * entities not changed will not be saved
					 */
					if ($entity->isDirty() === false) {
						$this->commit();
						return $this;
					}
					$query = new SelectQuery();
					$query->setDescription('check entity existence query');
					$query->table($entity->getSchema()->getName(), 's')->all();
					$value = null;
					foreach (($primaryList = $entity->getPrimaryList()) as $property) {
						if (($value = $property->get()) === null) {
							break;
						}
						$query->where()->and()->eq($property->getName(), 's')->to($value);
					}
					/**
					 * pickup an entity from storage if it's already there (and run update)
					 */
					$method = 'insert';
					foreach ($value ? $this->execute($query) : [] as $_) {
						$method = 'update';
						break;
					}
					$this->{$method}($entity);
					$this->commit();
					return $this;
				} catch (\Throwable $throwable) {
					$this->rollback();
					throw $throwable;
				}
			}

			/**
			 * @inheritdoc
			 */
			public function insert(IEntity $entity): IStorage {
				$this->saveRelations($entity);
				$this->execute(new InsertQuery($entity->getSchema(), $this->prepare($entity)));
				$entity->commit();
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function push(string $schema, array $source): IEntity {
				$this->insert($entity = $this->entityManager->create($schema, $source));
				return $entity;
			}

			/**
			 * @inheritdoc
			 */
			public function update(IEntity $entity): IStorage {
				$this->saveRelations($entity);
				$query = new UpdateQuery($entity->getSchema(), $this->prepare($entity));
				foreach ($entity->getPrimaryList() as $property) {
					$query->where()->and()->eq($property->getName())->to($property->get());
				}
				$this->execute($query);
				$entity->commit();
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function createSchema(string $schema): IStorage {
				/**
				 * because storage is using IQL in general, it's possible to safely use queries here in abstract
				 * implementation
				 */
				$this->execute(new CreateSchemaQuery($this->schemaManager->load($schema)));
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function collection(string $schema, IQuery $query = null): ICollection {
				if ($query === null) {
					/**
					 * by default select all from the source schema
					 */
					$query = new SelectQuery();
					$query->table($schema)->all();
				}
				return new Collection($this->entityManager, $this, $this->schemaManager->load($schema), $query);
			}

			/**
			 * @param IEntity $entity
			 *
			 * @return array
			 */
			protected function prepare(IEntity $entity): array {
				$source = $this->driver->toArray($entity);
				foreach ($entity->getPrimaryList() as $property) {
					$source[$property->getName()] = $property->get();
				}
				$entity->push($source = $this->schemaManager->generate($schemaName = $entity->getSchema()->getName(), $source));
				return $this->schemaManager->sanitize($schemaName, $source);
			}
		}
