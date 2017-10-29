<?php
	namespace Edde\Common\Query;

		use Edde\Api\Query\INativeBatch;
		use Edde\Api\Query\INativeQuery;
		use Edde\Common\Object\Object;

		class NativeBatch extends Object implements INativeBatch {
			/**
			 * @var INativeQuery[]
			 */
			protected $queryList = [];

			/**
			 * @inheritdoc
			 */
			public function add(INativeQuery $nativeQuery): INativeBatch {
				$this->queryList[] = $nativeQuery;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function getIterator() {
				return $this->queryList;
			}
		}
