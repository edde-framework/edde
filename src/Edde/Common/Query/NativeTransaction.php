<?php
	declare(strict_types=1);
	namespace Edde\Common\Query;

		use Edde\Api\Query\INativeQuery;
		use Edde\Api\Query\INativeTransaction;
		use Edde\Common\Object\Object;

		class NativeTransaction extends Object implements INativeTransaction {
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