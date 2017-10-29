<?php
	namespace Edde\Common\Query;

		use Edde\Api\Query\INativeBatch;
		use Edde\Api\Query\INativeQuery;

		class NativeBatch extends NativeQuery implements INativeBatch {
			/**
			 * @var INativeQuery[]
			 */
			protected $queryList = [];

			public function __construct($query = '', array $parameterList = []) {
				parent::__construct($query, $parameterList);
				if ($query) {
					$this->queryList[] = $this;
				}
			}

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
			public function addQuery($query, array $parameterList = []): INativeBatch {
				$this->add(new NativeQuery($query, $parameterList));
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function getIterator() {
				return new \ArrayIterator($this->queryList);
			}
		}
