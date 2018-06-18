<?php
	declare(strict_types=1);
	namespace Edde\Service\Transaction;

	trait Transaction {
		/** @var \Edde\Transaction\ITransaction */
		protected $transaction;

		/**
		 * @param \Edde\Transaction\ITransaction $transaction
		 */
		public function injectTransaction(\Edde\Transaction\ITransaction $transaction): void {
			$this->transaction = $transaction;
		}
	}
