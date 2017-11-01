<?php
	declare(strict_types=1);
	namespace Edde\Common\Storage;

		use Edde\Api\Driver\Inject\Driver;
		use Edde\Api\Query\INativeQuery;
		use Edde\Api\Query\Inject\QueryBuilder;
		use Edde\Api\Query\IQuery;
		use Edde\Api\Query\ISelectQuery;
		use Edde\Api\Storage\Exception\ExclusiveTransactionException;
		use Edde\Api\Storage\Exception\NoTransactionException;
		use Edde\Api\Storage\IStorage;
		use Edde\Api\Storage\IStream;
		use Edde\Common\Object\Object;

		class Storage extends Object implements IStorage {
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
			public function execute(IQuery $query) {
				return $this->driver->transaction($this->queryBuilder->query($query));
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
			public function stream(ISelectQuery $selectQuery): IStream {
				return new Stream($this, $selectQuery);
			}
		}
