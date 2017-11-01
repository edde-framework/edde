<?php
	namespace Edde\Common\Query;

		use Edde\Api\Query\INativeQuery;
		use Edde\Api\Query\INativeTransaction;

		class NativeTransaction extends NativeQuery implements INativeTransaction {
			/**
			 * @var INativeQuery[]
			 */
			protected $queryList = [];

			/**
			 * @inheritdoc
			 */
			public function query(INativeQuery $nativeQuery): INativeTransaction {
				$this->queryList[] = $nativeQuery;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function getIterator() {
				return new \ArrayIterator($this->queryList);
			}
		}
