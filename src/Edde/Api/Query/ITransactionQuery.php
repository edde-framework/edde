<?php
	declare(strict_types=1);
	namespace Edde\Api\Query;

	/**
	 * Just one query in a transaction.
	 */
		interface ITransactionQuery extends INativeQuery, INativeTransaction {
		}
