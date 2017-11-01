<?php
	namespace Edde\Common\Storage;

		use Edde\Api\Driver\Inject\Driver;
		use Edde\Api\Query\INativeQuery;
		use Edde\Api\Query\INativeTransaction;
		use Edde\Api\Query\Inject\QueryBuilder;
		use Edde\Api\Query\IQuery;
		use Edde\Api\Schema\Inject\SchemaManager;
		use Edde\Api\Storage\Exception\ExclusiveTransactionException;
		use Edde\Api\Storage\Exception\NoTransactionException;
		use Edde\Api\Storage\ICollection;
		use Edde\Api\Storage\Inject\EntityManager;
		use Edde\Api\Storage\IStorage;
		use Edde\Common\Object\Object;
		use Edde\Common\Query\CreateSchemaQuery;
		use Edde\Common\Query\SelectQuery;

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
			public function transaction(INativeTransaction $nativeTransaction) {
				try {
					$this->start();
					$result = $this->driver->transaction($nativeTransaction);
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
			public function execute(IQuery $query) {
				return $this->transaction($this->queryBuilder->build($query));
			}

			/**
			 * @inheritdoc
			 */
			public function query(INativeQuery $nativeQuery) {
				return $this->driver->execute($nativeQuery);
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
		}
