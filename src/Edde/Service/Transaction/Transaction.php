<?php
	declare(strict_types=1);
	namespace Edde\Service\Transaction;

	trait Transaction {
		/** @var \Edde\Storage\ITransaction */
		protected $transaction;

		/**
		 * @param \Edde\Storage\ITransaction $transaction
		 */
		public function injectTransaction(\Edde\Storage\ITransaction $transaction): void {
			$this->transaction = $transaction;
		}
	}
