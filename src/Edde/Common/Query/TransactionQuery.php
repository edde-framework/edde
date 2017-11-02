<?php
	declare(strict_types=1);
	namespace Edde\Common\Query;

		use Edde\Api\Query\INativeQuery;
		use Edde\Api\Query\INativeTransaction;
		use Edde\Api\Query\ITransactionQuery;

		class TransactionQuery extends NativeQuery implements ITransactionQuery {
			/**
			 * @var INativeTransaction
			 */
			protected $nativeTransation;

			public function __construct($query = null, array $parameterList = []) {
				parent::__construct($query, $parameterList);
				$this->nativeTransation = new NativeTransaction();
				if ($query) {
					$this->nativeTransation->query($this);
				}
			}

			/**
			 * @inheritdoc
			 */
			public function query(INativeQuery $nativeQuery) : INativeTransaction {
				$this->nativeTransation->query($nativeQuery);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function getIterator() {
				return $this->nativeTransation->getIterator();
			}
		}
