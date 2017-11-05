<?php
	declare(strict_types=1);
	namespace Edde\Api\Entity\Inject;

		use Edde\Api\Entity\ITransaction;

		trait Transaction {
			/**
			 * @var ITransaction
			 */
			protected $transaction;

			/**
			 * @param ITransaction $transaction
			 */
			public function lazyTransaction(ITransaction $transaction) {
				$this->transaction = $transaction;
			}
		}
