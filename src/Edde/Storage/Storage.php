<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Inject\Driver\Driver;
	use Edde\Object;
	use Edde\Query\IQuery;
	use Throwable;

	class Storage extends Object implements IStorage {
		use Driver;
		/** @var int */
		protected $transaction = 0;

		/** @inheritdoc */
		public function start(bool $exclusive = false): IStorage {
			if ($this->transaction > 0) {
				if ($exclusive === false) {
					$this->transaction++;
					return $this;
				}
				throw new TransactionException('Cannot start an exclusive transaction; there is already another one running.');
			}
			$this->driver->start();
			$this->transaction++;
			return $this;
		}

		/** @inheritdoc */
		public function commit(): IStorage {
			if ($this->transaction === 0) {
				throw new TransactionException('Cannot commit a transaction - there is no one running!');
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

		/** @inheritdoc */
		public function rollback(): IStorage {
			if ($this->transaction === 0) {
				throw new TransactionException('Cannot rollback a transaction - there is no one running!');
			} else if ($this->transaction === 1) {
				$this->driver->rollback();
			}
			$this->transaction--;
			return $this;
		}

		/** @inheritdoc */
		public function transaction(callable $callback) {
			$this->start();
			try {
				$result = $callback();
				$this->commit();
				return $result;
			} catch (Throwable $exception) {
				$this->rollback();
				throw $exception;
			}
		}

		/** @inheritdoc */
		public function execute(IQuery $query) {
			return $this->driver->execute($query);
		}

		/** @inheritdoc */
		public function stream(IQuery $query): IStream {
			return new Stream($this, $query);
		}

		/** @inheritdoc */
		public function fetch($query, array $params = []) {
			return $this->driver->fetch($query, $params);
		}

		/** @inheritdoc */
		public function exec($query, array $params = []) {
			return $this->driver->exec($query, $params);
		}
	}
