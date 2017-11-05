<?php
	declare(strict_types=1);
	namespace Edde\Api\Query;

		use Edde\Api\Entity\ITransaction;

		interface ITransactionQuery extends IQuery {
			/**
			 * @return ITransaction
			 */
			public function getTransaction(): ITransaction;
		}
