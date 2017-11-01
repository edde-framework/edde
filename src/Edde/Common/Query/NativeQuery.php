<?php
	declare(strict_types=1);
	namespace Edde\Common\Query;

		use Edde\Api\Query\INativeQuery;
		use Edde\Common\Object\Object;

		class NativeQuery extends Object implements INativeQuery {
			protected $query;
			protected $parameterList;

			public function __construct($query, array $parameterList = []) {
				$this->query = $query;
				$this->parameterList = $parameterList;
			}

			/**
			 * @inheritdoc
			 */
			public function getQuery() {
				return $this->query;
			}

			/**
			 * @inheritdoc
			 */
			public function getParameterList(): array {
				return $this->parameterList;
			}
		}
