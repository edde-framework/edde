<?php
	declare(strict_types=1);
	namespace Edde\Common\Query;

		use Edde\Api\Entity\ITransaction;
		use Edde\Api\Query\ITransactionQuery;

		class TransactionQuery extends AbstractQuery implements ITransactionQuery {
			/**
			 * @var ITransaction
			 */
			protected $transaction;

			/**
			 * @param ITransaction $transaction
			 */
			public function __construct(ITransaction $transaction) {
				$this->transaction = $transaction;
			}

			/**
			 * @inheritdoc
			 */
			public function getTransaction(): ITransaction {
				return $this->transaction;
			}
		}
