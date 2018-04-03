<?php
	declare(strict_types=1);
	namespace Edde\Transaction;

	use Edde\Obj3ct;
	use Throwable;

	abstract class AbstractTransaction extends Obj3ct implements ITransaction {
		/** @var int */
		protected $transaction = 0;

		/** @inheritdoc */
		public function start(): ITransaction {
			if ($this->transaction > 0) {
				$this->transaction++;
			}
			$this->onStart();
			$this->transaction++;
			return $this;
		}

		/** @inheritdoc */
		public function commit(): ITransaction {
			if ($this->transaction === 0) {
				throw new TransactionException('Cannot commit a transaction - there is no one running!');
			} else if ($this->transaction === 1) {
				$this->onCommit();
			}
			/**
			 * it's intentional to lower the number of transaction after commit as a driver could throw an
			 * exception, thus transaction state could not be consistent
			 */
			$this->transaction--;
			return $this;
		}

		/** @inheritdoc */
		public function rollback(): ITransaction {
			if ($this->transaction === 0) {
				throw new TransactionException('Cannot rollback a transaction - there is no one running!');
			} else if ($this->transaction === 1) {
				$this->onRollback();
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
				throw new TransactionException(sprintf('Transaction failed: %s', $exception->getMessage()), 0, $exception);
			}
		}

		abstract protected function onStart(): void;

		abstract protected function onCommit(): void;

		abstract protected function onRollback(): void;
	}
