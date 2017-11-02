<?php
	namespace Edde\Common\Query;

		use Edde\Api\Query\Exception\QueryException;
		use Edde\Api\Query\INativeQuery;
		use Edde\Api\Query\INativeTransaction;
		use Edde\Api\Query\ITransactionQuery;

		class TransactionQuery extends NativeQuery implements ITransactionQuery {
			/**
			 * @inheritdoc
			 */
			public function query(INativeQuery $nativeQuery) : INativeTransaction {
				throw new QueryException('Cannot use single transaction query for multiple queries.');
			}

			/**
			 * @inheritdoc
			 */
			public function getIterator() {
				yield $this;
			}
		}
